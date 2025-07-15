#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Rembg功能测试脚本
用于验证Rembg库是否正确安装和工作
"""

import os
import sys
from pathlib import Path

def test_rembg_installation():
    """测试Rembg库是否正确安装"""
    try:
        import rembg
        print("✅ Rembg库安装成功")
        print(f"版本: {rembg.__version__}")
        return True
    except ImportError as e:
        print(f"❌ Rembg库安装失败: {e}")
        print("请运行: pip install rembg")
        return False

def test_pillow_installation():
    """测试Pillow库是否正确安装"""
    try:
        from PIL import Image
        print("✅ Pillow库安装成功")
        return True
    except ImportError as e:
        print(f"❌ Pillow库安装失败: {e}")
        print("请运行: pip install Pillow")
        return False

def create_test_image():
    """创建一个简单的测试图片"""
    try:
        from PIL import Image, ImageDraw
        
        # 创建一个简单的测试图片
        img = Image.new('RGB', (200, 200), color='white')
        draw = ImageDraw.Draw(img)
        
        # 画一个红色圆形作为前景
        draw.ellipse([50, 50, 150, 150], fill='red')
        
        test_image_path = "test_image.png"
        img.save(test_image_path)
        print(f"✅ 测试图片已创建: {test_image_path}")
        return test_image_path
    except Exception as e:
        print(f"❌ 创建测试图片失败: {e}")
        return None

def test_background_removal(test_image_path):
    """测试背景移除功能"""
    try:
        from rembg import remove
        
        print("正在测试背景移除功能...")
        
        # 读取测试图片
        with open(test_image_path, 'rb') as input_file:
            input_data = input_file.read()
        
        # 移除背景
        output_data = remove(input_data)
        
        # 保存结果
        output_path = "test_image_no_bg.png"
        with open(output_path, 'wb') as output_file:
            output_file.write(output_data)
        
        print(f"✅ 背景移除测试成功! 结果保存为: {output_path}")
        return True
        
    except Exception as e:
        print(f"❌ 背景移除测试失败: {e}")
        return False

def cleanup_test_files():
    """清理测试文件"""
    test_files = ["test_image.png", "test_image_no_bg.png"]
    for file_path in test_files:
        if os.path.exists(file_path):
            try:
                os.remove(file_path)
                print(f"已清理测试文件: {file_path}")
            except Exception as e:
                print(f"清理文件失败 {file_path}: {e}")

def main():
    """主测试函数"""
    print("=== Rembg功能测试 ===\n")
    
    # 测试库安装
    if not test_rembg_installation():
        return False
    
    if not test_pillow_installation():
        return False
    
    print("\n=== 开始功能测试 ===")
    
    # 创建测试图片
    test_image_path = create_test_image()
    if not test_image_path:
        return False
    
    # 测试背景移除
    success = test_background_removal(test_image_path)
    
    # 清理测试文件
    cleanup_test_files()
    
    if success:
        print("\n🎉 所有测试通过! Rembg库工作正常")
        return True
    else:
        print("\n❌ 测试失败")
        return False

if __name__ == "__main__":
    success = main()
    sys.exit(0 if success else 1) 