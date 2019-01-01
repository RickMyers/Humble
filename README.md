# The Humble Project

        ____                       ___                 ______    ___ __  _
       / __ \____ __________ _____/ (_)___ _____ ___  / ____/___/ (_) /_(_)___  ____
      / /_/ / __ `/ ___/ __ `/ __  / / __ `/ __ `__ \/ __/ / __  / / __/ / __ \/ __ \
     / ____/ /_/ / /  / /_/ / /_/ / / /_/ / / / / / / /___/ /_/ / / /_/ / /_/ / / / /
    /_/    \__,_/_/   \__,_/\__,_/_/\__, /_/ /_/ /_/_____/\__,_/_/\__/_/\____/_/ /_/
                                   /____/


Humble is a framework unlike any other.  Humble is an MVC framework where the Controllers are written in XML,
the Views can be written in any templating language like Smarty3, TBS, Twig, PHPTAL, Mustache, etc... and the
Models are either implied using a native Polyglot ORM, or written as Plain Old PHP Objects  (POPO).  Further,
webservices and RPC are handled by crafting YAML files which define the interactions between the sources, and
finally the Paradigm Engine, which is at the humble of the framework, scans your Model classes cataloging your
methods and allowing you to construct business logic by drag-and-dropping those methods into a workflow  which
is then compiled.

### Paradigm Engine
![Demo Workflow](https://humble.enicity.com/pages/images/screengrabs/workflow_1.png "Demo Workflow")

The workflow shown above is composed of exposed methods contained within your models.  These methods are categorized by the type of functionality they perform, such
as PROCESS, INPUT, NOTIFICATION (e-mail, text, pop-up alert...), DECISION, etc... which you identify using annotations in your code, as shown below:

```php
    /**
     * Returns true if you've exceeded a set number of failed login attempts.  The number of attempts is set by
     * way of the configuration page identified below
     *
     * @workflow use(decision) configuration(workflow/user/tries)
     * @param \Core\Event\BaseObject $EVENT
     * @return boolean
     */
    public function exceededTries($EVENT=false) {
        $exceeded = true;
        if ($EVENT) {
            $data   = $EVENT->load();
            if (isset($data['user_name'])) {
                $user       = Humble::getEntity('humble/users')->setUserName($data['user_name'])->load(true);
                $config     = $EVENT->fetch();
                if (isset($config['tries']) && ($config['tries'])) {
                    $exceeded   = ((int)$user->getLoginAttempts() > (int)$config['tries']);
                }
            }
        }
        return $exceeded;
    }

```

Once a workflow has been built and all components (glyphs) have been mapped to the appropriate methods, you are able to compile the diagram into a working workflow
as shown below:

![Workflow Compiled](https://humble.enicity.com/pages/images/screengrabs/workflow_2.png "Workflow Compiled")

## Controllers

Controllers are XML files that follow a syntax very similar to Apache ANT.  These files manage object
allocation, data verification, conditional processing, and routing.  A sample partial controller is shown below.
```xml
<?xml version="1.0"?>
<controller name="user" use="Twig">
    <actions>

        <action name="login" event='userLogin' comment='Triggers the login workflow'>
            <description>Launches the process that a person goes through to login</description>
            <model namespace="humble" class="user" id="user">
                <parameter name="user_name" source="post" default="" required='true' />
                <parameter name="password"  source="post" value="user_password" format="password" required='true' />
            </model>
            <switch id='user' method='login'>
                <case value='TRUE'>
                    <model namespace='humble' class='user' method='routeToHomePage'>

                    </model>
                </case>
                <default>
                    <redirect href='/index.html?m=Invalid Login Attempt' />
                </default>
            </switch>
        </action>

        <!-- ############################################################### -->

        <action name="new">
            <description>Prompts the user to enter their password for the first time</description>
            <model namespace="humble" class="user" id="user">
                <parameter name="new_password_token" value='token' source="get" default="" />
            </model>
        </action>

    <!-- ############################################################### -->

        <action name="default">
            <description>Default Action</description>
            <model namespace="humble" class="user" id="user" method="invite">
                <parameter name="email" source="request" default="" />
            </model>
        </action>
```
### Clean URLs

Humble projects are a collection of modules, and each module has its own namespace.  Every class, JavaScript file, template, etc are referenced using that namespace. Each action is referenced in
the following way:

_/namespace/controller/view_

Some examples:

>/humble/home/admin

>/paradigm/actions/open

>/acme/employee/list

>/acme/employee/add

>/acme/employee/save

>/acme/employee/delete

Thus all of Humble's actions are REST friendly, and the default output is JSON.  If you wish to present a view using server-side rendering, templates can be written in any of several different templating languages (Smarty3, Twig, Mustache, TBS, etc..)

### Webservice (RPC) Descriptor

Humble abstracts the complex details of data exchange by representing the remote calls, be they REST, SOAP, or standard POST/GET calls, in YAML.  In this way, a moderately
technical person can craft a YAML file from an API specification that the developers would then be able to use like a local function call.  Below is an example of REST, SOAP,
and a normal GET call to remote sources:

```yaml
---
flickrPhotosetList :
  url             : https://api.flickr.com/services/rest/
  api-key         :
  api-var         : api_key
  secure          : true
  method          : GET
  arguments       :
        method      : flickr.photosets.getList
        user_id     :

flickrGalleryList :
  url             : https://api.flickr.com/services/rest/
  api-key         :
  api-var         : api_key
  secure          : true
  method          : GET
  arguments       :
        method      : flickr.people.findByusername
        user_id     :
currentWeather  :
  url             : http://weather.yahooapis.com/forecastrss
  api-key         :
  api-var         :
  arguments       : [p]
  method          : GET
acmehealthAuthentication   :
  wsdl            : https://acme.humble.acmehealthplatform.com/ServicesARGUS/AuthenticationService.svc?wsdl  #production
  version         : 1.2
  operation       : Login
  ws-addressing   :
    Namespace     : ns2
    Action        : http://www.Acmeation.com/Authentication/IAuthentication/Login
    ReplyTo       : http://www.w3.org/2005/08/addressing/anonymous
    To            : https://acme.humble.acmehealthplatform.com/ServicesARGUS/AuthenticationService.svc/Authentication
  method          : SOAP
  arguments       :
    loginName     :
    loginPswd     :
    ipAddress     :
unlockUser:
  wsdl            : https://acme-uat.humble.acmehealthplatform.com/ServicesARGUS/UserAccountService.svc?wsdl  #UAT
  version         : 1.2
  operation       : ProviderUserAccountUnlock
  ws-addressing   :
    Namespace     : ns2
    Action        : http://www.Acmeation.com/Authentication/IAuthentication/ProviderUserAccountUnLock
    ReplyTo       : http://www.w3.org/2005/08/addressing/anonymous
    To            : https://acme-uat.humble.acmehealthplatform.com/ServicesARGUS/UserAccountService.svc/UserAccount
  method          : SOAP
  arguments       :
      ProviderUserAccountLockRequest :
        SessionId   :
        UserGID     :
```
Here is a real quick video of building a module to manage the MySQL demonstration tables found here: https://dev.mysql.com/doc/employee/en/

<iframe width="900" height="675" src="https://www.youtube.com/embed/jCoUNIUmSjI" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>

## A final note...

_**Don't Fork And Run!**_  You won't be getting what you think you are getting.  The proper way to install this framework for testing is to visit the project home page
at https://humble.enicity.com and make sure you have the dependencies properly installed and configured.  You will also need to have an Apache web server running and your VHOST
configured properly.

Any questions?  The Author of this framework can be reached here... <a href="mailto:rickmyers1969@gmail.com?subject=Humble%20Framework">Rick Myers</a>