### 图片可进行处理项

    1. 生成略缩图
    2. 圆形处理
    3. 获取图片resource类型资源
    4. 裁剪
    5. 水印
    6. 压缩
    
### 获取图片资源

     可以将http、https、物理路径、图片字符串类型的图片源转化成resource类型。 
      
     使用gd库操作图片大部分需要使用resource类型的图片源。 
       
     1. Image类
        $imageUrl='';
        $image=new Image($imageUrl);
        $image->get();
        
     2. Resources类
        Resources::instance()->get($imageUrl);
        
        第二个参数可以指定使用的文件格式，如png，string，https等
        
### 远程图片处理
     远程图片处理： 
        1. 处理http://xxx.com/xx.ext或者https://xxx.com/xx.ext
            ext可以是png、jpg、gif等明确的文件格式
            直接使用imagecreatefrompng()等函数处理
            
        2. 处理http:/xxx.com/xxx或者https://xxx.com/xx
            没有指明文件格式的，可以使用php的curl请求，打印头编码查看Content-type:xx 的类型
        
        3. 处理图片字符串可以使用imagecreatefromstring
        
### 合成文字方式1
    注意：imagefttext中字体的大小使用的是磅值（point）
     
    1. 创建画布,大小100px*100px
    
        $canvas=Resources::instance()->create(100,100);
        
    2. 创建文字
        $canvas=Resources::instance()->createText($string, $size, $fontFile, [220, 220, 255]);
       
### 合成文字方式2
    use magicfier\Image;
    
    $image = new Image();
    $image->mergeText('测试文字', $fontPath, 18, 0, 0);
    $image->output('jpg');
    
### 合成图片方式1   
    imagecopy($dst_resource,$src_resource,$dst_x,$dst_y,$src_x,$src_y,$src_w,$src_y);
    
    将图片处理成resource类型使用imagecopy函数处理即可
    
### 合成图片方式2 
    use magicfier\Image;
    
    $image = new Image();
    
    $path = realpath(__DIR__);
    $avatar = $path.'/../public/avatar.jpg';
    $image->mergeImage($avatar, 'png', 350,350, 200,200);
    $image->output('jpg', true);
    
    
### 图片输出或保存
    调用$image->output()输出到浏览器
    调用$image->output($extend, true)输出到浏览器, 文件下载
    调用$image->save()保存
    调用$image->toString()输出字符串, 默认png 默认base64
     
    配合header输出到浏览器
     header('Content-type:image/png')
     imagepng($resource);
     
    保存图片
     imagepng($resource,$filename);
     
    保存图片大小
     imagepng($resource,$filename,20);
     第三个参数是图片的质量，数字越大，保真度越高，对应的文件越大
        
### 注意释放内存
    
    输出，保存后记得释放内存
    
    imagedestroy($resource);    
    
### 调试
    
    在将图片资源转化resource类型的时候，可能会出现失败的情况，返回null
     
    如果在使用中需要输出失败的信息，可以
    $image->$closeError=false;
    设置后，返回null的时候会抛出异常，默认关闭     
    
    
                
        
    
