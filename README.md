Nette PHP Generator
===================



Installation
------------
```
composer require linphp/generator
```


generator使用说明
-----



```php
命令1.  php think gen index  #根据数据表生成所有controller,service,model,的文件。
命令2.  php think gen index@user #生成 index模块下的 user控制器文件 userService文件,model文件，
命令3.  php think gen  #如果有修改数据表操作请执行此命令,重构app/model映射文件夹,保证映射表关系。
提示:
    1.正在使用generator时请配置好数据库,保证能正确连接。
    2.生成文件说明
        |--app
            |--index
                 |--controller #自动生成的控制器文件
                 |--model #自动生成的模型,sql语句可以在这里写。
                 |--service #自动生成的逻辑层代码,
            |--model  #此文件夹禁止修改文件
                 |--entity #自动生成的映射模型,此文件夹下文件是对应所有数据表中的映射模型,禁止写入代码,每次使用php think gen index或index@user会重构结构表   



```


