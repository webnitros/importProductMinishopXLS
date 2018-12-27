miniShop2.plugin.count = {
    getFields: function () {
        return {
            count: {
                xtype: 'numberfield',
                fieldLabel: 'Основной склад',
                description: '<b>[[+count]]</b><br />' + 'Основной склад'
            },
            storage_corp: {
                xtype: 'numberfield',
                fieldLabel: 'Корпаративный склад',
                description: '<b>[[+storage_corp]]</b><br />' + 'Корпаративный склад'
            },
            storage_transit: {
                xtype: 'numberfield',
                fieldLabel: 'Транзитный склад',
                description: '<b>[[+storage_transit]]</b><br />' + 'Транзитный склад'
            },

            baseunit: {
                xtype: 'textfield',
                fieldLabel: 'Упаковка',
                description: '<b>[[+baseunit]]</b><br />' + 'Упаковка'
            },
            cat_number: {
                xtype: 'textfield',
                fieldLabel: 'Каталожный номер',
                description: '<b>[[+cat_number]]</b><br />' + 'Каталожный номер'
            },
            sov_number: {
                xtype: 'textfield',
                fieldLabel: 'Совм номер',
                description: '<b>[[+sov_number]]</b><br />' + 'Совм номер'
            },
            brand: {
                xtype: 'textfield',
                fieldLabel: 'Бренд',
                description: '<b>[[+brand]]</b><br />' + 'Бренд'
            },

            price_2: {
                xtype: 'numberfield',
                fieldLabel: 'Цена 2',
                decimalPrecision: 2,
                description: '<b>[[+price_2]]</b><br />' + 'Цена 2'
            },
            price_4: {
                xtype: 'numberfield',
                fieldLabel: 'Цена 4',
                decimalPrecision: 2,
                description: '<b>[[+price_4]]</b><br />' + 'Цена 4'
            },
        }
    },
    getColumns: function () {
        return {}
    }
};