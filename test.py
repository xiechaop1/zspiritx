import pygame

def main():
    # 初始化 pygame 的 mixer 模块
    pygame.mixer.init()
    # 加载背景音乐
    pygame.mixer.music.load('a.mp3')
    # 开始播放背景音乐，-1 表示循环播放
    pygame.mixer.music.play(-1)
    # 设置背景音乐的音量，范围是 0.0 到 1.0
    pygame.mixer.music.set_volume(0.5)
    
    # 加载语音文件
    voice = pygame.mixer.Sound('b.mp3')
    # 播放语音，播放一次
    voice.play()

    # 等待语音播放完毕
    while pygame.mixer.get_busy():
        pygame.time.Clock().tick(10)


if __name__ == "__main__":
    main()
