# 介绍

Hyperf-Admin 基于Hyperf v1.1 开发的前后分离管理后台

演示地址：[http://mrw.so/4I6mCR](http://mrw.so/4I6mCR) (账号密码：admin/admin)

**QQ 交流群：235816687**

## 热加载开发

运行根目录下的 zls-watch,一个技术朋友分享的,go写的一个插件,可以直接运行,其他命令可以查看 ./zls-watch -H

    chmod +x ./zls-watch
    ./zls-watch
    
## 安装步骤

`docker run -v /docker/www/hyperf-api:/hyperf-skeleton -p 8080:9501 -it --entrypoint /bin/bash hyperf/hyperf:7.2-alpine-cli-4.4.7`

* ###### -v /docker/www/hyperf-api:/hyperf-skeleton
    >/docker/www/hyperf-api：宿主机目录 ， /hyperf-skeleton：docker目录
    
* ###### -p 8080:9501
    > 8080:本地端口 ， 9501：docker下Hyperf配置文件servers的端口

## 功能介绍

- 管理员管理
- 角色管理
- 菜单管理
- 参数管理
- OSS管理
- 日志管理

![输入图片说明](https://raw.githubusercontent.com/penghcheng/hyperf-admin/master/screenshot/login.png "01.png")
![输入图片说明](https://raw.githubusercontent.com/penghcheng/hyperf-admin/master/screenshot/user.png "01.png")






  




   
