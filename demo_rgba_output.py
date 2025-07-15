#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
RGBA PNG输出演示脚本
展示Rembg库生成的透明背景PNG图像
"""

import os
from pathlib import Path
from PIL import Image, ImageDraw
from rembg import remove

def create_demo_image():
    """创建一个演示图片，包含复杂背景"""
    # 创建一个带复杂背景的图片
    img = Image.new('RGB', (400, 300), color='lightblue')
    draw = ImageDraw.Draw(img)
    
    # 画一些背景元素
    draw.rectangle([0, 0, 400, 100], fill='green')  # 草地
    draw.rectangle([0, 200, 400, 300], fill='brown')  # 土地
    
    # 画一些装饰元素
    for i in range(5):
        x = 50 + i * 80
        draw.ellipse([x, 50, x+20, 70], fill='yellow')  # 花朵
    
    # 画主要对象（红色圆形）
    draw.ellipse([150, 100, 250, 200], fill='red')
    draw.ellipse([170, 120, 230, 180], fill='darkred')
    
    demo_path = "demo_image.jpg"
    img.save(demo_path, quality=95)
    print(f"✅ 演示图片已创建: {demo_path}")
    return demo_path

def demonstrate_rgba_output(input_path):
    """演示RGBA PNG输出"""
    print("\n=== RGBA PNG输出演示 ===")
    
    # 读取原始图片
    with open(input_path, 'rb') as f:
        input_data = f.read()
    
    # 使用Rembg移除背景
    print("正在移除背景...")
    output_data = remove(input_data)
    
    # 保存为PNG
    output_path = "demo_output_rgba.png"
    with open(output_path, 'wb') as f:
        f.write(output_data)
    
    # 分析输出图像
    with Image.open(output_path) as img:
        print(f"\n📊 输出图像信息:")
        print(f"   格式: {img.format}")
        print(f"   模式: {img.mode}")
        print(f"   尺寸: {img.size}")
        print(f"   颜色通道: {len(img.getbands())}")
        
        if img.mode == 'RGBA':
            print(f"   Alpha通道: 存在 (透明度)")
            
            # 检查透明度
            alpha = img.getchannel('A')
            transparent_pixels = sum(1 for pixel in alpha.getdata() if pixel < 255)
            total_pixels = img.size[0] * img.size[1]
            transparency_ratio = transparent_pixels / total_pixels * 100
            
            print(f"   透明像素: {transparent_pixels}/{total_pixels} ({transparency_ratio:.1f}%)")
            print(f"   🎨 背景: 完全透明")
        else:
            print(f"   ⚠️ 警告: 不是RGBA格式")
    
    print(f"\n✅ 演示完成! 输出文件: {output_path}")
    print(f"💡 这个PNG文件可以在任何支持透明背景的软件中使用")
    
    return output_path

def create_comparison_image(original_path, rgba_path):
    """创建对比图，展示透明背景效果"""
    try:
        # 打开原始图片和RGBA图片
        with Image.open(original_path) as original:
            with Image.open(rgba_path) as rgba:
                # 创建一个带网格的背景
                bg = Image.new('RGB', (800, 300), color='white')
                draw = ImageDraw.Draw(bg)
                
                # 画网格
                for i in range(0, 800, 20):
                    draw.line([(i, 0), (i, 300)], fill='lightgray')
                for i in range(0, 300, 20):
                    draw.line([(0, i), (800, i)], fill='lightgray')
                
                # 粘贴原始图片
                bg.paste(original, (50, 50))
                
                # 粘贴RGBA图片（会显示透明背景）
                bg.paste(rgba, (450, 50), rgba)
                
                # 添加标签
                draw.text((200, 20), "原始图片", fill='black')
                draw.text((600, 20), "透明背景", fill='black')
                
                comparison_path = "comparison.png"
                bg.save(comparison_path)
                print(f"✅ 对比图已创建: {comparison_path}")
                return comparison_path
                
    except Exception as e:
        print(f"❌ 创建对比图失败: {e}")
        return None

def main():
    """主函数"""
    print("=== RGBA PNG输出演示 ===\n")
    
    # 创建演示图片
    demo_path = create_demo_image()
    
    # 演示RGBA输出
    rgba_path = demonstrate_rgba_output(demo_path)
    
    # 创建对比图
    comparison_path = create_comparison_image(demo_path, rgba_path)
    
    print(f"\n🎉 演示完成!")
    print(f"📁 生成的文件:")
    print(f"   - {demo_path} (原始图片)")
    print(f"   - {rgba_path} (RGBA PNG，透明背景)")
    if comparison_path:
        print(f"   - {comparison_path} (对比图)")
    
    print(f"\n💡 使用说明:")
    print(f"   - RGBA PNG文件可以在Photoshop、GIMP等软件中打开")
    print(f"   - 透明背景可以叠加到任何其他图片上")
    print(f"   - 适合用于电商产品图、设计素材等")

if __name__ == "__main__":
    main() 