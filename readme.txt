
1. Добавляет к товарам дополнительные метаполя описанные в файле api - основное.docx. Все эти метаполя отображаются в главных настройках товара.
2. Изменяет поля ввода при оформлении заказа.
3. Отправляет заказы в кристалл (не используется).
4. Добавляет страницу опций:
   - идентификатор магазина
   - выгружать ли заказы в кристалл, адрес для выгрузки
   - ссылка для перехода и оплаты в кристалл (при оформлении заказа)
5. Маска ввода и валидация телефона
6. Выбор Физ.лицо, ИП или Юр.лицо при оформлении заказа. Ввод и проверка ИНН, ОГРН. Ввод наименования организации. 
7. Реализация логики продажи одно обучение-один заказ для товаров с соответствующей пометкой
8. Реализация логики продажи только Физ.лицам для товаров с соответствующей пометкой
9. Скрытие товаров дочерних категорий на странице родительских категорий. Разворачивание содерджимого папки Uncategirized на главную страницу магазино. Сокрытие папки Uncategirized.
10. Шорткод для отображения истории заказов из кукисов [orders_cookies_history].
Релиз
11. автоматическое обновление корзины при изменении количества заказов
12. чекбокс публичной оферты при оформлении заказа
Релиз 2019-05-08
13. Изменена страница настроек: URL API кристала - как единая точка доступа
14. Поддержка шорткодов [kristal_private_ads count=7 type=All], [kristal_trade_place count=7]. Шорткод kristal_vebinars добавлен, но реализации нет
Релиз 2019-07-22
15. "key": "only_for_fiz_lico" - Больше не существует
    Добавлено:
    "key": "allow_client_types_fiz" - физ.лицо
    "key": "allow_client_types_ip" - ип
    "key": "allow_client_types_yur" - юр.лицо
    Если true - то данный товар приобретается в только теми типами пользователей которые отмечены true. Логика оформления
    такая: в чекауте скрываются варианты выбора (Физ.лица, ИП и Юр.лица), которым нельзя покупать товары в корзине.
    Если у товара все три значения false, то это приравнивается как будто бы вcе три - true.
    При установке (активации) плагина, он пробегается по всем товарам и для тех у кого only_for_fiz_lico = true, устанавливает
    allow_client_types_fiz = true
    allow_client_types_ip = false
    allow_client_types_yur = false
    при этом старое поле (only_for_fiz_lico) не удаляется, не изменяется и больше никак не используется
Релиз 2020-07-05

