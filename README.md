<p align="center">
    <a href="https://github.com/yiisoft" target="_blank">
        <img src="https://avatars0.githubusercontent.com/u/993323" height="100px">
    </a>
    <h1 align="center">Yii 2 Advanced Project Template</h1>
    <br>
</p>

Yii 2 Advanced Project Template is a skeleton [Yii 2](http://www.yiiframework.com/) application best for
developing complex Web applications with multiple tiers.

The template includes three tiers: front end, back end, and console, each of which
is a separate Yii application.

The template is designed to work in a team development environment. It supports
deploying the application in different environments.

Documentation is at [docs/guide/README.md](docs/guide/README.md).

[![Latest Stable Version](https://img.shields.io/packagist/v/yiisoft/yii2-app-advanced.svg)](https://packagist.org/packages/yiisoft/yii2-app-advanced)
[![Total Downloads](https://img.shields.io/packagist/dt/yiisoft/yii2-app-advanced.svg)](https://packagist.org/packages/yiisoft/yii2-app-advanced)
[![Build Status](https://travis-ci.org/yiisoft/yii2-app-advanced.svg?branch=master)](https://travis-ci.org/yiisoft/yii2-app-advanced)

DIRECTORY STRUCTURE
-------------------

```
common
    config/              contains shared configurations
    mail/                contains view files for e-mails
    models/              contains model classes used in both backend and frontend
    tests/               contains tests for common classes    
console
    config/              contains console configurations
    controllers/         contains console controllers (commands)
    migrations/          contains database migrations
    models/              contains console-specific model classes
    runtime/             contains files generated during runtime
backend
    assets/              contains application assets such as JavaScript and CSS
    config/              contains backend configurations
    controllers/         contains Web controller classes
    models/              contains backend-specific model classes
    runtime/             contains files generated during runtime
    tests/               contains tests for backend application    
    views/               contains view files for the Web application
    web/                 contains the entry script and Web resources
frontend
    assets/              contains application assets such as JavaScript and CSS
    config/              contains frontend configurations
    controllers/         contains Web controller classes
    models/              contains frontend-specific model classes
    runtime/             contains files generated during runtime
    tests/               contains tests for frontend application
    views/               contains view files for the Web application
    web/                 contains the entry script and Web resources
    widgets/             contains frontend widgets
vendor/                  contains dependent 3rd-party packages
environments/            contains environment-based overrides
```

# 图片背景移除工具

这是一个使用Rembg库自动提取图片主景并移除背景的Python工具。

## 功能特点

- 🖼️ 自动识别并提取图片主景
- 🎯 智能移除背景，保留主体内容
- 🎨 **输出RGBA PNG格式，背景完全透明**
- 📁 支持单文件和批量处理
- 🔧 支持alpha matting技术，提供更精细的边缘处理
- 💻 提供命令行和交互式两种使用方式

## 安装依赖

```bash
pip install -r requirements.txt
```

或者手动安装：

```bash
pip install rembg Pillow numpy
```

## 使用方法

### 1. 交互式使用

直接运行脚本，按提示操作：

```bash
python image_background_remover.py
```

### 2. 命令行使用

#### 单文件处理

```bash
# 基本用法
python image_background_remover.py input.jpg

# 指定输出路径
python image_background_remover.py input.jpg -o output.png

# 使用alpha matting技术
python image_background_remover.py input.jpg --alpha-matting

# 自定义alpha matting参数
python image_background_remover.py input.jpg --alpha-matting --foreground-threshold 240 --background-threshold 10 --erode-size 10
```

#### 批量处理

```bash
# 批量处理文件夹中的所有图片
python image_background_remover.py /path/to/images -b

# 指定输出文件夹
python image_background_remover.py /path/to/images -b -o /path/to/output
```

### 3. 在代码中使用

```python
from image_background_remover import remove_background

# 基本用法
remove_background("input.jpg")

# 指定输出路径
remove_background("input.jpg", "output.png")

# 使用alpha matting
remove_background("input.jpg", "output.png", alpha_matting=True)
```

## 支持的图片格式

### 输入格式
- JPEG (.jpg, .jpeg)
- PNG (.png)
- BMP (.bmp)
- TIFF (.tiff)
- WebP (.webp)

### 输出格式
- **PNG (RGBA)** - 带透明背景，支持Alpha通道
- 背景完全透明，适合叠加到其他图片上

## 参数说明

### Alpha Matting 参数

- `--alpha-matting`: 启用alpha matting技术，提供更精细的边缘处理
- `--foreground-threshold`: 前景阈值 (默认: 240)
- `--background-threshold`: 背景阈值 (默认: 10)
- `--erode-size`: 腐蚀大小 (默认: 10)

### 输出文件命名

- 单文件处理：自动在原文件名后添加 `_no_bg.png` 后缀
- 批量处理：在指定输出文件夹中创建带 `_no_bg.png` 后缀的文件
- **注意：输出始终为PNG格式，支持透明背景**

## 示例

### 处理单张图片

```bash
python image_background_remover.py photo.jpg
# 输出: photo_no_bg.png (RGBA格式，透明背景)
```

### 批量处理

```bash
python image_background_remover.py ./photos -b
# 在 ./photos/removed_background/ 文件夹中生成处理后的PNG图片
```

### 使用高级参数

```bash
python image_background_remover.py photo.jpg --alpha-matting --foreground-threshold 250 --background-threshold 5
```

## 注意事项

1. 首次运行时会自动下载Rembg模型文件，需要网络连接
2. 处理大图片时可能需要较长时间
3. 建议在处理前备份原始图片
4. Alpha matting技术可以提供更好的边缘效果，但处理时间更长
5. **输出文件始终为PNG格式，支持透明背景，适合用于设计、电商等场景**

## 演示RGBA输出

运行演示脚本查看RGBA PNG输出效果：

```bash
python demo_rgba_output.py
```

这个脚本会：
- 创建一个测试图片
- 使用Rembg移除背景
- 生成RGBA PNG文件（透明背景）
- 创建对比图展示效果
- 显示详细的图像信息（格式、尺寸、透明度等）

## 故障排除

### 常见问题

1. **模型下载失败**: 检查网络连接，或手动下载模型文件
2. **内存不足**: 尝试处理更小的图片或关闭其他程序
3. **处理效果不理想**: 尝试调整alpha matting参数或使用不同的图片
4. **输出不是透明背景**: 确保输出文件是PNG格式，Rembg默认输出RGBA PNG

### 错误信息

- `输入文件不存在`: 检查文件路径是否正确
- `处理过程中出现错误`: 查看具体错误信息，可能是图片格式不支持或文件损坏

## 许可证

本项目使用MIT许可证。
