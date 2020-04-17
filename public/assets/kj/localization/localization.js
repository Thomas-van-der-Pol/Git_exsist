var kjlocalization = {
    notFoudText: 'Unknown translation, please publish translations!',

    create: function(category, keyValuePairs) {

        categoryFormatted = category.toLowerCase().replace(' ', '_');
        keyValuePairsChecked = [];

        // Door keyValuePairs heen lopen om te controleren of deze al zijn ingevoerd. Onnoddige calls uitsluiten
        keyValuePairs.forEach(function (keyValuePair, index) {

            $.each(keyValuePair, function(key, value) {
                keyFormatted = key.toLowerCase().replace(' ', '_');

                if (kjlocalization.get(categoryFormatted, keyFormatted) === kjlocalization.notFoudText) {
                    keyValuePairsChecked.push(keyValuePair);
                }
            });
        });

        if (keyValuePairsChecked.length > 0) {
            data = new FormData();
            data.append('category', category);
            data.append('keyValuePairs', JSON.stringify(keyValuePairsChecked));

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: window.i18n.createTranslationUrl,
                type: 'POST',
                data: data,
                contentType: false,
                processData: false
            });
        }
    },

    get: function(category, key) {
        var i18nCategory = i18n[category];
        if (i18nCategory === undefined) {
            return kjlocalization.notFoudText;
        }

        var translated = i18nCategory[key];
        if (translated === undefined) {
            return kjlocalization.notFoudText;
        } else {
            return translated;
        }
    }
};