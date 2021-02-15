(function() {
    tinymce.PluginManager.add('wpse_141344_tinymce_button', function( editor, url ) {
        editor.addButton( 'wpse_141344_tinymce_button', {
            text : ' ',
            icon : ' tiny-smiley-smile',
            title : 'Smileys',
            type: 'menubutton',
            menu: [
                {
                    text: ':@',
                    icon: ' tiny-smiley-angry',
                    value: ':@',
                        onclick: function() {
                            editor.insertContent(this.value());
                        }
                },
                {
                    text: ':D',
                    icon: ' tiny-smiley-bigsmile',
                    value: ':D',
                        onclick: function() {
                            editor.insertContent(this.value());
                        }
                },
                {
                    text: ':|',
                    icon: ' tiny-smiley-nosmile',
                    value: ':|',
                        onclick: function() {
                            editor.insertContent(this.value());
                        }
                },
                {
                    text: 'o.O',
                    icon: ' tiny-smiley-oO',
                    value: 'o.O',
                        onclick: function() {
                            editor.insertContent(this.value());
                        }
                },
                {
                    text: '^^',
                    icon: ' tiny-smiley-raise',
                    value: '^^',
                        onclick: function() {
                            editor.insertContent(this.value());
                        }
                },
                {
                    text: ':(',
                    icon: ' tiny-smiley-sad',
                    value: ':(',
                        onclick: function() {
                            editor.insertContent(this.value());
                        }
                },
                {
                    text: ':O',
                    icon: ' tiny-smiley-shocked',
                    value: ':O',
                        onclick: function() {
                            editor.insertContent(this.value());
                        }
                },
                {
                    text: ':)',
                    icon: ' tiny-smiley-smile',
                    value: ':)',
                        onclick: function() {
                            editor.insertContent(this.value());
                        }
                },
                {
                    text: ':P',
                    icon: ' tiny-smiley-tongue',
                    value: ':P',
                        onclick: function() {
                            editor.insertContent(this.value());
                        }
                },
                {
                    text: ';)',
                    icon: ' tiny-smiley-wink',
                    value: ';)',
                        onclick: function() {
                            editor.insertContent(this.value());
                        }
                }
                
           ]

        });
    });
})();


