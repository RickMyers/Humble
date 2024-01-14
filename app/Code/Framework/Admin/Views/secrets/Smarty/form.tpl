<style type="text/css">
    .secret_field_desc {
        padding-bottom: 15px; font-family: monospace; font-size: .85em; letter-spacing: 1px
    }
</style>
<div  id='thisscript'></div>
<script type="text/javascript"> 
    if (typeof add_secret_app === 'undefined') {
        let add_secret_app = Vue.createApp({
            template: Humble.template('admin/newsecret'),
            data: () => {
                return {
                    namespaces: [
                        {
                            "text": "",
                            "value": ""
                        },
                        {foreach from=$modules->fetch() item=module}{              
                            text:   "{$module.namespace|ucfirst}",
                            value:  "{$module.namespace}"
                        },   
                        {/foreach}
                    ]
                }
            },
            methods: {
                saveDetails: async () => {
                    if (Edits['new-secret'].validate()) {
                        const form = document.querySelector('#user_details_form');
                        const response = await fetch('/admin/user/save', {
                            method: 'POST',
                            body: new FormData(form)
                        });
                        let text = await response.text();
                        alert(text);
                    }   
                }
            }
        });
        console.log('{$window_id}');
        console.log(Desktop.window.list['{$window_id}'].content);
        add_secret_app.mount(Desktop.window.list['{$window_id}'].content);
        
    }    
    (function () {
        new EasyEdits('/edits/admin/newsecret','new-secret');
    })();
</script>
