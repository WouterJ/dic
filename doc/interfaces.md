# Aanpassen met Interface

Als je met grotere applicaties werkt ga je gebruik maken van aware interfaces,
bijv. `EventDispatcherAwareInterface`. Deze interfaces geven aan dat deze
class de `EventDispatcher` kan opgeslaan. Je hebt vaak een methode als
`setEventDispatcher` waarin je de event dispatcher injecteert. Maar dit moet
je elke keer controlleren en zo ja de event dispatcher injecteren:

````php
// ...
$registration = $container->get('Registration');

if ($registration instanceof MailerAwareInterface) {
    $registration->setMailer($container->get('Mailer'));
}
````

Dit is natuurlijk hartstikke onhandig. Nu kun je wel een aparte factory aanmaken
die het object aanmaakt en vervolgens deze setter aanroept:

````php
$container->setFactory('registration', function ($c) {
    $registration = $c->get('Registration');
    $registration->setMailer($c->get('Mailer'));

    return $registration;
});
````

Maar alsnog moet je dit per klasse instellen. Onze container zou niet slim
heten als hij dit niet zou kunnen en daarom maken we nu kennis met de
`InitializeManager`. Deze is net zoals de `InstanceManager` geintegreerd in de
container, maar niet bij aanmaken beschikbaar (wel als je hem gaat gebruiken).

Met deze manager kun je een *Initializer* aanmaken. Dit is ongeveer hetzelfde
als een factory, met de uitzondering dat deze vlak voor het teruggeven van de
service wordt aangeroepen. De service is dan al gemaakt en jij kunt de
instance gaan aanpassen.

Deze initializers stel je in op bepaalde interfaces. In ons voorbeeld hierboven
gaat de code worden:

````php
// ...
$container->setInitializer('MailerAwareInterface', function ($instance, $c) {
    $instance->setMailer($c->get('Mailer'));
});
````

Nu wordt bij elke class die `MailerAwareInterface` implementeert deze
initializer aangeroepen, zodra hij via de container wordt opgevraagd. Het
eerste argument dat hij meekrijgt is de instance, een object, en het 2e 
argument is de container. Deze initializer returned niks, hij past alleen het 
object aan.
