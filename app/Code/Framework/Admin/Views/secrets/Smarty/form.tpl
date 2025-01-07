<style type="text/css">
    .secret_field_desc {
        padding-bottom: 15px; font-family: monospace; font-size: .85em; letter-spacing: 1px
    }
</style>
<div  id='thisscript'></div>
<script type="text/javascript"> 
    if (typeof add_secret_app === 'undefined') {
        let add_secret_app = Vue.createApp({
            template: Humble.template('admin','secrets/new'),
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
            mounted() {
                new EasyEdits('/edits/admin/newsecret','new-secret');
            },

            methods: {
                addSecret: async () => {
                    if (Edits['new-secret'].validate()) {
                        const form = document.querySelector('#new_secret_form');
                        const response = await fetch('/admin/secrets/new', {
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
        const secret_app = add_secret_app.mount('#'+win.content.id);
        win.close(((app) => {
            return function () {
                app.unmount();
            };
        })(add_secret_app));
    }    

</script>
<script type='module'>
    import Counter from '/comp/admin/counter.vue';
</script>
