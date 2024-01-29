<script type="text/javascript"> 
    if (typeof component_form_app === 'undefined') {
        let component_form_app = Vue.createApp({
            template: Humble.template('admin','component/new'),
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
                    ],
                    packages: [
                        {
                            "text": "",
                            "value": ""
                        },
                        {foreach from=$packages->fetch() item=package}{              
                            text:   "{$package.text}",
                            value:  "{$package.text}"
                        },   
                        {/foreach}
                        
                    ],
                    categories: [
                        {
                            "text": "",
                            "value": ""
                        },
                        {foreach from=$categories->fetch() item=category}{              
                            text:   "{$category.text}",
                            value:  "{$category.text}"
                        },   
                        {/foreach}
                    ]
                }
            },
            mounted: () => {
                new EasyEdits('/edits/admin/newcomponent','component-form');
            },
            created: () => {

            },
            updated: () => {

            },
            destroyed: () => {

            },
            methods: {
                addSecret: async () => {
                    if (Edits['component-form'].validate()) {
                        const form = document.querySelector('#new_secret_form');
                        const response = await fetch('/admin/component/new', {
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
        const secret_app = component_form_app.mount('#'+win.content.id);
        win.close(((app) => {
            return function () {
                app.unmount();
            };
        })(component_form_app));
    }    

</script>


