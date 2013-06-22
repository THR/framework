#İsimsiz framework

Bu, bir kaç framework'ün güzel yanlarını ve ihtiyaçlarımı harmanlayıp, kendime hafif bir framework yazma çabasıdır.
İsmi henüz konmamıştır, -bundan öncekinin adı kebap framework'dü-.

Şimdilik olmasını planladığım özellikler

- [x] Hierarchical MVC -HMVC- (Modüller için)
- [ ] PDO tabanlı basit bir query builder sınıf ve basit bir orm
- [ ] Scaffolding -CRUD sayfaları oluşturmak için-
- [x] Regex'e imkan tanıyan bir Router
- [x] HTTP Requestlerini düzgünce işleyebilmek için Request sınıfı
- [ ] i18n, l10n -çoklu dil desteği şart-
- [x] Basit render motoru
- [ ] Tak-kullan sınıflar
- [x] PHP ile event zor ama Hooks iş görür bence, (beforeRequest, beforeSave, onUserRegister vs)
- [x] Restful
- [ ] Render motoruyla ilişkili theme ve layout
- [ ] ve en önemlisi boyutu mümkün olduğunca küçük olsun

## Server Gereksinimleri
PHP 5.3 ya da daha yeni

##Hooks
Örneğin Router sınıfı hangi controllerın çalışacağına karar vermeden önce birşeyler yapmak, araya kanca atmak için:

-çakma eventListener-
```php

Hooks::add('route.beforeDecide',function(){
  // Burada requeste bakıp istek işlenmeden önce cache çalıştırılabilir.
});
```

Kendi kancanızı fırlatmak için:
-çakma triggerEvent-
```php

Hooks::fire('kanca.adi',array('foo'='bar'));

```
Yakalamak için
```php
Hooks::add('kanca.adi',function($param){
  echo $param; // 'bar' yazar
});
```

## Router
Buna biraz bramus, slim framework, klein router vs. den esinlendim, hoşuma gitti.
İsim verme fikri Symfony'den geldi. Böylece link oluşturma kolaylaştı.

```php
Router::add('blog.index','blog','blog/default/index');
```

'blog.index' isimli yönlendirme kuralımız, site.com/blog isteğini yakalayacak ve dispatch işlemi için 'blog/default/index' işlemine gönderecek.

###Regex kullanımı ve parametre alma

```php
Router::add('blog.post','blog/<slug:\w+>.html','blog/default/show');
```

'blog.post' isimli kural, şunları yakalayabilir.
*site.com/blog/lorem.html, site.com/blog/tahir.html*

Post id'si -ya da başka bir identifier'ı- **blog** modülünün **default** controllerının **show** action'ınına parametre olarak gidecek.

yani defaultController.php de
```php
public function actionShow($postID)
{
  echo $postID;
}
```

için

site.com/blog/tahir.html 'e girildiğinde $postID, 'tahir' olacaktır.
aynı şekilde bu değişkene global olarak $_GET['slug'] ile de ulaşabilirsiniz. BKZ: 'blog.post' route kuralı.

Bu kurala link oluşturmak için

```php
Router::createLink('blog.post',array('slug'=>'tahir'));
```

kodunu kullanabilirsiniz.
Döndüreceği değer: site.com/blog/tahir.html olacaktır.

## Theme
Yii'nin tema sistemi hoşuma gitmişti. Buna benzer birşey yapmak lazım.

varsayılan bir viewPath olur, eğer tema ayarlanmışsa view dosyası için önce tema dizini altındaki viewe bakacak. Yoksa varsayılan viewPath'e.


