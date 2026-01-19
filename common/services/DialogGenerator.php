<?php
/**
 * AI对话生成器服务
 * 用于生成符合dialogdoc.txt规范的对话内容
 * User: Claude
 * Date: 2026/1/13
 */

namespace common\services;

use yii\base\Component;
use Yii;

class DialogGenerator extends Component
{
    /**
     * 生成对话的主方法
     * @param string $userDescription 用户描述的对话需求
     * @param string $existingDialog 现有的对话内容
     * @param string $modelName 模型名称(用于对话中的name字段)
     * @param string $modelInstUId 模型实例UID(用于localID的前缀)
     * @param string $version 版本选择: 'simple'=简化版, 'full'=完整版
     * @return string 生成或合并后的对话代码
     */
    public function generateDialog($userDescription, $existingDialog = '', $modelName = '', $modelInstUId = '', $version = 'simple')
    {
        try {
            // 如果modelInstUId为空，使用modelName作为后备
            if (empty($modelInstUId)) {
                $modelInstUId = $modelName;
            }

            // 1. 构建AI Prompt
            $prompt = $this->buildPrompt($userDescription, $modelName, $modelInstUId, $version);

            // 2. 调用Doubao服务
            $aiResponse = $this->callAI($prompt, $modelName, $modelInstUId);

            // 3. 解析AI返回的内容
            $newDialog = $this->parseAIResponse($aiResponse, $modelName, $modelInstUId);

            // 4. 智能合并对话
            if (empty($existingDialog) || trim($existingDialog) === '') {
                // 如果现有对话为空,直接返回新对话
                return $newDialog;
            } else {
                // 如果有现有对话,进行智能合并
                return $this->mergeDialogs($existingDialog, $newDialog, $modelInstUId);
            }
        } catch (\Exception $e) {
            Yii::error('DialogGenerator Error: ' . $e->getMessage());
            throw new \Exception('对话生成失败: ' . $e->getMessage());
        }
    }

    /**
     * 构建AI Prompt
     * @param string $description 用户描述
     * @param string $modelName 模型名称(用于对话中的name字段)
     * @param string $modelInstUId 模型实例UID(用于localID的前缀)
     * @param string $version 版本选择: 'simple'=简化版, 'full'=完整版
     * @return array Prompt消息数组
     */
    private function buildPrompt($description, $modelName, $modelInstUId, $version = 'simple')
    {
        // 根据版本选择读取不同的prompt文件
        if ($version === 'full') {
            // 完整版：读取dialogfull.txt
            $promptFilePath = Yii::getAlias('@backend') . '/dialogfull.txt';
        } else {
            // 简化版：读取dialogdoc.txt
            $promptFilePath = Yii::getAlias('@backend') . '/dialogdoc.txt';
        }

        // 读取prompt文件内容
        if (!file_exists($promptFilePath)) {
            throw new \Exception("Prompt文件不存在: {$promptFilePath}");
        }

        $promptContent = file_get_contents($promptFilePath);

        // 替换变量
        $promptContent = str_replace('{$modelInstUId}', $modelInstUId, $promptContent);
        $promptContent = str_replace('{$modelName}', $modelName, $promptContent);

        $messages = [];

        // 系统角色
        $messages[] = [
            'role' => 'system',
            'content' => '#角色#' . "\n" . '你是一个专业的AR游戏对话系统设计师,精通对话脚本编写和PHP数组结构。'
        ];

        // 对话规范（使用完整的prompt文件内容）
        $messages[] = [
            'role' => 'system',
            'content' => '#对话规范#' . "\n" . $promptContent
        ];

        // 任务要求（基础要求）
        $taskRequirement = <<<EOT
#任务要求#
请根据用户的描述,生成完整的对话数组。注意:
1. localID必须按照"模型实例UID-dialog-序号"的格式,例如:"{$modelInstUId}-dialog-0"（注意：这里使用的是模型实例UID，不是模型名称）
2. 要分析出对话的具体人名，放到name字段中，比如：name: 对话内容
EOT;

        // 如果使用dialogdoc.txt（简化版），添加优先级说明
        if ($version === 'simple') {
            $taskRequirement .= "\n3. **重要：如果有冲突，优先遵循上述对话规范文档中的规范**";
        }

        $messages[] = [
            'role' => 'system',
            'content' => $taskRequirement
        ];

        // 用户需求
        $userContent = "模型名称(用于name字段): {$modelName}\n模型实例UID(用于localID前缀): {$modelInstUId}\n需求描述: {$description}";
        $messages[] = [
            'role' => 'user',
            'content' => $userContent
        ];

        return $messages;
    }

    /**
     * 提取dialogdoc.txt的关键部分
     * @param string $dialogDoc 完整的dialogdoc.txt内容
     * @return string 关键部分
     */
    private function extractKeyParts($dialogDoc)
    {
        // 提取关键示例部分,避免prompt过长
        $lines = explode("\n", $dialogDoc);
        $keyLines = [];

        // 提取基本框架部分(前20行)
        $keyLines = array_merge($keyLines, array_slice($lines, 0, 20));

        // 提取对话条结构部分(第70-210行)
        $keyLines = array_merge($keyLines, array_slice($lines, 69, 140));

        // 提取完整示例部分(第256-356行)
        $keyLines = array_merge($keyLines, array_slice($lines, 255, 100));

        return implode("\n", $keyLines);
    }

    /**
     * 调用Doubao AI服务
     * @param array $messages Prompt消息
     * @param string $modelName 模型名称
     * @param string $modelInstUId 模型实例UID
     * @return string AI返回的内容
     */
    private function callAI($messages, $modelName, $modelInstUId)
    {
        $doubao = Yii::$app->doubao;

        // 使用Doubao的chatWithDoubao方法
        // 参数: $userMessage, $oldMessages, $templateContents, $roleTxts, $isJson, $modelParams

        // 分离消息
        $userMessage = '';
        $roleTxts = [];
        $templateContents = [];

        foreach ($messages as $msg) {
            if ($msg['role'] === 'system') {
                $templateContents[] = $msg['content'];
            } elseif ($msg['role'] === 'user') {
                $userMessage = $msg['content'];
            }
        }

        // 不使用JSON格式,因为我们需要返回PHP代码
        $response = $doubao->chatWithDoubao($userMessage, [], $templateContents, $roleTxts, false);

        Yii::info('DialogGenerator AI Response: ' . var_export($response, true));

        if (empty($response)) {
            throw new \Exception('AI服务返回为空');
        }

        return $response;
    }

    /**
     * 解析AI返回的内容
     * @param string $response AI返回的内容
     * @param string $modelName 模型名称
     * @param string $modelInstUId 模型实例UID
     * @return string 格式化后的PHP数组代码
     */
    private function parseAIResponse($response, $modelName = '', $modelInstUId = '')
    {
        // 如果返回的已经是字符串,直接使用
        if (is_string($response)) {
            $phpCode = $response;
        } else {
            $phpCode = var_export($response, true);
        }

        // 清理可能的markdown标记
        $phpCode = str_replace('```php', '', $phpCode);
        $phpCode = str_replace('```', '', $phpCode);
        $phpCode = trim($phpCode);

        // 检测是否是简单对话流格式（以引号开头，不是array(开头）
        $isSimpleFormat = false;
        if (preg_match('/^\s*[\'"]/', $phpCode)) {
            $isSimpleFormat = true;
        }

        // 如果是简单对话流格式，需要包装成完整的array结构
        if ($isSimpleFormat) {
            // 提取对话内容（假设是逗号分隔的字符串数组）
            // 包装成完整的Dialog数组结构，包含Name、Intro等字段
            $intro = !empty($modelInstUId) ? $modelInstUId . '-dialog-0' : 'auto-0';
            $phpCode = "array (\n  'Name' => '" . addslashes($modelName) . "',\n  'Intro' => '{$intro}',\n  'Dialog' => \n  array (\n" . $phpCode . "\n  ),\n)";
        }

        // 验证是否是有效的PHP数组代码
        // 尝试eval来验证(在沙盒环境中)
        try {
            $testArray = eval('return ' . $phpCode . ';');
            if (!is_array($testArray)) {
                throw new \Exception('AI返回的内容不是有效的PHP数组');
            }

            // 验证必要的字段
            if (!isset($testArray['Dialog']) || !is_array($testArray['Dialog'])) {
                throw new \Exception('AI返回的数组缺少Dialog字段');
            }
        } catch (\Exception $e) {
            Yii::error('ParseAIResponse Error: ' . $e->getMessage() . "\nResponse: " . $phpCode);
            throw new \Exception('AI返回的格式不正确,请重试');
        }

        return $phpCode;
    }

    /**
     * 智能合并对话
     * @param string $existingDialog 现有对话代码
     * @param string $newDialog 新生成的对话代码
     * @param string $modelInstUId 模型实例UID
     * @return string 合并后的对话代码
     */
    private function mergeDialogs($existingDialog, $newDialog, $modelInstUId)
    {
        try {
            // 1. 解析现有对话
            $existingArray = eval('return ' . $existingDialog . ';');
            $newArray = eval('return ' . $newDialog . ';');

            // 2. 提取现有对话的最大ID
            $maxId = $this->extractMaxDialogId($existingDialog, $modelInstUId);

            // 3. 调整新对话的ID,从maxId+1开始
            $adjustedNewArray = $this->adjustDialogIds($newArray, $maxId + 1, $modelInstUId);

            // 4. 找到现有对话的最后一个非空对话,修改其nextID
            $existingDialogs = $existingArray['Dialog'];
            $lastNonEmptyIndex = -1;
            for ($i = count($existingDialogs) - 1; $i >= 0; $i--) {
                if (!empty($existingDialogs[$i]['sentence']) || !empty($existingDialogs[$i]['url'])) {
                    $lastNonEmptyIndex = $i;
                    break;
                }
            }

            // 如果找到了最后一个非空对话,修改其nextID指向新对话的第一条
            if ($lastNonEmptyIndex >= 0 && !empty($adjustedNewArray['Dialog'][0]['localID'])) {
                $existingDialogs[$lastNonEmptyIndex]['nextID'] = array(
                    $adjustedNewArray['Dialog'][0]['localID']
                );
            }

            // 5. 合并Dialog数组
            $mergedArray = $existingArray;
            $mergedArray['Dialog'] = array_merge($existingDialogs, $adjustedNewArray['Dialog']);

            // 6. 转换回PHP代码
            $mergedCode = var_export($mergedArray, true);

            // 7. 格式化:去掉数组索引
            $mergedCode = preg_replace('/\s*\d+\s*=>\s*/', "\n", $mergedCode);

            return $mergedCode . ';';

        } catch (\Exception $e) {
            Yii::error('MergeDialogs Error: ' . $e->getMessage());
            throw new \Exception('对话合并失败: ' . $e->getMessage());
        }
    }

    /**
     * 提取现有对话的最大ID序号
     * @param string $dialog 对话代码
     * @param string $modelInstUId 模型实例UID
     * @return int 最大ID序号
     */
    private function extractMaxDialogId($dialog, $modelInstUId)
    {
        $maxId = -1;

        // 正则匹配所有的 localID => 'modelInstUId-dialog-数字'
        $pattern = "/'localID'\s*=>\s*'" . preg_quote($modelInstUId, '/') . "-dialog-(\d+)'/";
        preg_match_all($pattern, $dialog, $matches);

        if (!empty($matches[1])) {
            foreach ($matches[1] as $id) {
                $maxId = max($maxId, intval($id));
            }
        }

        // 同时检查 end1, end2 等结束对话的ID
        $endPattern = "/'localID'\s*=>\s*'" . preg_quote($modelInstUId, '/') . "-dialog-end(\d+)'/";
        preg_match_all($endPattern, $dialog, $endMatches);

        if (!empty($endMatches[0])) {
            // 如果有end对话,说明对话结构完整,返回maxId
            // end对话不参与ID计数
        }

        return $maxId;
    }

    /**
     * 调整对话数组的ID
     * @param array $dialogArray 对话数组
     * @param int $startId 起始ID
     * @param string $modelInstUId 模型实例UID
     * @return array 调整后的对话数组
     */
    private function adjustDialogIds($dialogArray, $startId, $modelInstUId)
    {
        if (!isset($dialogArray['Dialog'])) {
            return $dialogArray;
        }

        $dialogs = $dialogArray['Dialog'];
        $idMap = []; // 旧ID -> 新ID的映射
        $currentId = $startId;
        $endCount = 1;

        // 第一遍:创建ID映射
        foreach ($dialogs as $dialog) {
            if (isset($dialog['localID'])) {
                $oldId = $dialog['localID'];

                // 判断是否是end对话
                if (preg_match('/-dialog-end\d*$/', $oldId)) {
                    $newId = $modelInstUId . '-dialog-end' . $endCount;
                    $endCount++;
                } else {
                    $newId = $modelInstUId . '-dialog-' . $currentId;
                    $currentId++;
                }

                $idMap[$oldId] = $newId;
            }
        }

        // 第二遍:替换所有的ID引用
        $newDialogs = [];
        foreach ($dialogs as $dialog) {
            // 替换localID
            if (isset($dialog['localID']) && isset($idMap[$dialog['localID']])) {
                $dialog['localID'] = $idMap[$dialog['localID']];
            }

            // 替换nextID数组中的引用
            if (isset($dialog['nextID']) && is_array($dialog['nextID'])) {
                $newNextIds = [];
                foreach ($dialog['nextID'] as $nextId) {
                    if (isset($idMap[$nextId])) {
                        $newNextIds[] = $idMap[$nextId];
                    } else {
                        $newNextIds[] = $nextId;
                    }
                }
                $dialog['nextID'] = $newNextIds;
            }

            $newDialogs[] = $dialog;
        }

        // 更新Intro
        if (isset($dialogArray['Intro']) && isset($idMap[$dialogArray['Intro']])) {
            $dialogArray['Intro'] = $idMap[$dialogArray['Intro']];
        }

        $dialogArray['Dialog'] = $newDialogs;

        return $dialogArray;
    }
}
