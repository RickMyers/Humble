<style type="text/css">
    .secret_field_desc {
        padding-bottom: 15px; font-family: monospace; font-size: .85em; letter-spacing: 1px
    }
</style>
<table style="width: 100%; height: 100%">
    <tr>
        <td style="height: 50px; vertical-align: bottom;  padding: 5px; color: ghostwhite; font-size: 2.5em; font-family: monospace; background-color: rgba(00,102,153,.8); border-bottom: 1px solid ghostwhite">
            SECRETS MANAGER
        </td>
    </tr>
    <tr>
        <td style="height: 10%; padding: 20px 5px 0px 5px; color: ghostwhite; font-size: 1em; font-weight: bolder;  font-family: monospace; background-color: rgba(00,102,153,.8)">
            A secret is an encrypted piece of text that can be an API Key, account credential, or other important piece of information that needs to be kept confidential.
            If setting an API Key for use in the mapping.yaml file, use the SM:// protocol to recover and decrypt your secret (Example: <i>api-key: SM://mySecretName</i>)<br /><br />            
        </td>
    </tr>
    <tr>
        <td style="height: 30%; background-color: rgba(00,102,153,.8); color: ghostwhite" id="add_secret_app"></td>
    </tr>
    <tr>
        <td height="*"  style=" background-color: rgba(00,102,153,.8); color: ghostwhite" id="review_secret_app"></td>
    </tr>   
    <tr>
        <td style="height: 20px; text-align: right; font-family: sans-serif; font-size: .8em; background-color: #333; color: ghostwhite; padding-right: 10px">&copy; Humbleprogramming.com, 2007-Present</td>
    </tr>
</table>
<script type="text/javascript"> 
    ((win) => {
        Humble.admin['secrets_manager'] = Humble.admin['secrets_manager'] ?? {};
        Humble.admin['secrets_manager']['add'] = Vue.createApp({
            template: Humble.template('admin','secrets/new'),
            data() {
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
                new EasyEdits('/edits/admin/newsecret','add-secret');
            },
            methods: {
                addSecret: async () => {
                    if (Edits['new-secret'].validate()) {
                        const form = document.querySelector('#add_secret_form');
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
        Humble.admin['secrets_manager']['add'].mount('#add_secret_app');
        win.close(((app) => {
            return function () {
                app.unmount();
            };
        })(Humble.admin['secrets_manager']['add']));
        Humble.admin['secrets_manager']['review'] = Vue.createApp({
            template: Humble.template('admin','secrets/review'),
            data() {
                return {
                    visible: false,
                    toggle_icon: false,
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
            mounted()  {
               new EasyEdits('/edits/admin/fetchsecret','fetch-secret');
               this.toggle_icon = document.getElementById('toggle_hidden_switch');
            },
            methods: {
                toggle(ref) {
                    let icon = $('#fetch_secret_form [name=secret_value]').get(0);
                    if (icon.type == 'password') {
                        icon.type = 'text';

                    } else {
                        icon.type = 'password'
                     }

                },            
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
        Humble.admin['secrets_manager']['review'].mount('#review_secret_app');
        win.close(((app) => {
            return function () {
                app.unmount();
            };
        })(Humble.admin['secrets_manager']['review']));
        win.resize((() => {
            return function () {
            }
        })())
    })(Desktop.window.list['{$window_id}']);
</script>