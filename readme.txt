1、MVC架构：
用户访问 controller
controller 调用 model 获取数据
controller 把 model 返回的数据注入 view.
controller 把 view 渲染结果返回给用户。 

2、项目整体调度：
项目中一般有多个 controller
加个入口文件 index.php 决定实例哪个controller
controller, model, view 的创建一般用工厂模式管理，
view 的 display 里可以做模板引擎，解析 html 模板

3、入口调度：
入口文件初始化框架所需的整体环境
跟据路由规则定位到一个控制器上，然后控制权就交给控制器了。

4、写框架需要了解的知识：
路由，数据库，session、cookie。get/post，命名空间，自动加载，

5、抽象类和接口类
看具体的对象行为，抽象类一般有一些特征可以共用一份代码。
接口则没有，接口用来描述共有特征，但具体实现不一样。
用接口成本是最低的，不过 php 接口比较新，应用不多。
大部分框架大量用抽像类。
抽象类中的方法，可以是已经实现了功能的函数；
接口类中的方法，只是提供方法名，由子类去实现方法功能
抽象类中实现了部分函数。还有一个特点，接口可以多继承。一个类可以继承多个接口，但只能继承一个父类；
新版本 php 有个 trait 可以实现类的多继承，一些较新的框架已经大量在用 trait 了，一个类可以继承多个 trait 类。
多用php新特性
命名空间，匿名函数，异常处理，php 7 里没有 error 了，全是异常。以后 php 代码里会到处是 try catch。

6、先看什么框架？可以模仿的。
Laravel
thinkphp
yii
先看tp，国产，比较好理解。

7、实际项目中是有多个控制器的。
而且分模块的，每个模块下有多个控制器。
入口文件跟据路由参数选择是哪个模块下的哪个控制器，及方法。
MODULE_NAME
CONTROLLER_NAME
ACTION_NAME
