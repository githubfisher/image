### 说明

     1. 文件编码遵循 psr-4 规范
     2. 支持本地，远程，字符串图片转化成系统可操作的resource变量类型
     3. 支持略缩图，圆形，裁剪，水印，压缩
     4. 调用对应的函数可输出到浏览器，可保存(保存到远程服务器请自行配置), 也可以直接转换成base64型编码输出
     5. 图片格式仅包含png,jpg,jpeg,gif（远程图片，图片字符串任意类型）
     6. 支持合成图片, 合成文字
     7. 支持自定义尺寸
### 使用

     具体使用说明及实例, 请阅读DOCUMENT.md
     
### 依赖

    1. php >= 5.5 
    2. gd库
    3. 远程https图片资源，需支持curl或者openssl        
     
### 建议

    在转化远程图片时，imagecreatefromjpeg等方法支持直接转化，https协议下，需要环境支持openssl
     
    linux系统下，开始openssl后出现证书等验证失败，可以使用curl获取到图片资源，然后使用imagecreatefromstring进行转化，  
     
    所以可根据实际情况友好的选择使用$image->transImageResource(), 第二个参数得到可操作的resource类型图片   
    
### 优化

    查看远程图片的类型，可以使用curl打印出头信息，查看Content-type:xx的值
     
    如：Content-type:image/png ,Content-type:image/jpeg等
    
### 下载
 
     composer require magicfier/image
     
### 使用

     use Magicfier/image;
     
     $image = new Image();
     $image->mergeImage($newImagePath, $extend, $width, $height, $top, $left, $originWidth, $originHeight);
     $image->output($extend);
     
