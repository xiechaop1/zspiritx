<?php

//$r = [
//		[['avatar', 'uname', 'mobile', 'email',
//				'birthday', 'work_start_date', 'current_company',
//				'current_post', 'foreign_language', 'university', 'job_status', 'expect_post',
//				'document_file', 'expect_salary', 'resource', 'expect_career',
//				'skills', 'certification', 'self_evaluation', 'interview',
//				'education_experience', 'work_experience', 'project_experience',
//				'education', 'national', 'language_skills', 'training',
//				'nation_group', 'birthplace', 'census', 'marry', 'political', 'gender',
//				'fertility', 'work_years', 'user_no', 'city_str', 'expect_city_str'], 'string'],
//		[['current_salary', 'other_income'], 'string'],
//		[['city_id', 'age',  'expect_industry', 'current_industry', 'document_status', 'created_at', 'updated_at', 'status'], 'integer'],
//];
//
//foreach ($r as $one) {
//	foreach ($one[0] as $col) {
//		echo 'public $' . $col . ";\n";
//	}
//}


$list = [1,2,3];
$num = 3;

function f($list, $num, $arrA, $arrB, $flag, $ret1, $ret2) {
    if ($flag == 1) {
        // 如果flag是1的话就从开始的位置取数字

        // 如果A和B的数组长度相等，就把数字给A，否则给B
        if (sizeof($arrA) == sizeof($arrB)) {
            $arrA[] = array_shift($list);
        } else {
            $arrB[] = array_shift($list);
        }
    } else {
        // 否则就从尾部的位置取数字

        // 如果A和B的数组长度相等，就把数字给A，否则给B
        if (sizeof($arrA) == sizeof($arrB)) {
            $arrA[] = array_pop($list);
        } else {
            $arrB[] = array_pop($list);
        }
    }

    // 如果剩余的牌只有1张了，就一定给某一个人，当前函数就结束了
    if (sizeof($list) == 1) {
        if (sizeof($arrA) == sizeof($arrB)) {
            $arrA[] = array_shift($list);
        } else {
            $arrB[] = array_shift($list);
        }

        // 求和，如果A的大于B的，就记录A赢1次，一共1次。否则就记录A赢0次，一共1次（数组返回）
        if (sum($arrA) > sum($arrB)) {
            return [1, 1];
        } else {
            return [0, 1];
        }
    }
    // 递归调用记录结果
    // 递归从头取
    $retTmp1 = f($list, $num, $arrA, $arrB, 1, $ret1, $ret2);
    $ret1 += $retTmp1[0];
    $ret2 += $retTmp1[1];
    // 递归从尾取
    $retTmp2 = f($list, $num, $arrA, $arrB, 0, $ret1, $ret2);
    $ret1 += $retTmp2[0];
    $ret2 += $retTmp2[1];

    return [
        $ret1, $ret2
    ];
}

// 数组求和函数
function sum($arr) {
    $r = 0;
    foreach ($arr as $one) {
        $r += $one;
    }
    return $r;
}

// 调用第一次（头部）
$result = f($list, $num, [], [], 1, 0, 0);
// 调用第二次（尾部）
$result = f($list, $num, [], [], 0, $result[0], $result[1]);

var_dump($result);
