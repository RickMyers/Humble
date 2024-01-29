<script type="text/javascript"> 
    if (typeof review_secret_app === 'undefined') {
        let review_secret_app = Vue.createApp({
            template: Humble.template('admin','secrets/review'),
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
            mounted: () => {
               new EasyEdits('/edits/admin/fetchsecret','fetch-secret');
            },
            created: () => {
            },
            updated: () => {
            },
            destroyed: () => {
            },
            methods: {
                addSecret: async () => {
                    if (Edits['fetch-secret'].validate()) {
                        const form = document.querySelector('#new_secret_form');
                        const response = await fetch('/admin/secrets/fetch', {
                            method: 'POST',
                            body: new FormData(form)
                        });
                        let text = await response.text();
                        alert(text);
                    }   
                }
            }
        });
        var win = Desktop.window.list['{$window_id}'];
        const secret_app = review_secret_app.mount('#'+win.content.id);
        win.close(((app) => {
            return function () {
                app.unmount();
            };
        })(review_secret_app));
    }    

</script>
