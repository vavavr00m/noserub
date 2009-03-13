<?php 

if($this->data) {
    echo $form->create(array('url' => '/admins/settings/'));

    echo '<fieldset><legend>' . __('Network', true) . '</legend>';
    echo $form->input('Network.url');
    echo '<p>';
    __('The URL, under which your NoseRub installation can be found. This could be http://myserver.com/ or http://myserver.com/stuff/<br />Please make sure, you do not include the noserub directory. That means, when you have NoseRub installed in http://myserver.com/noserub/, you would have to enter only http://myserver.com/ here. When your installation is available directly under http://myserver.com/, just put http://myserver.com/ here. Also, the URL must end with a /.');
    echo '</p>';
    
    echo $form->input('Network.name');
    echo '<p>';
    __('The name of this NoseRub network. This is used for the title and to identify this network at various points like notification eMails.');
    echo '</p>';
    
    echo $form->input('Network.description');
    echo '<p>';
    __('Try to describe what this network primarily deals with. NoseRub networks are often created with a specific topic in mind. The description will be made available for others to see.');
    echo '</p>';
    
    echo $form->label('Network.default_language', __('Default language', true));    
    echo $form->select('Network.default_language', Configure::read('Languages'), null, null, false);
    echo '<p>';
    __('NoseRub comes in multiply languages and tries to identify which language a user wants. If NoseRub is unable to determine this, it falls back to the language chosen here. You can also give a hint for other people which language this network prefers.');
    echo '</p>';
    echo '</fieldset>';
    
    echo '<fieldset><legend>' . __('Location', true) . '</legend>';
    echo '<p>';
    __('NoseRub will allow people to search for networks nearby. Therefore it can be interesting for your network to show where it locates itself. If you have no intention to bound your network to a specific area, just leave the Latitude and Longitude set to 0.');
    echo '</p>';
    
    echo $form->input('Network.latitude');
    echo $form->input('Network.longitude');
    
    # echo $form->input('Network.google_maps_key');
    echo '</fieldset>';
    
    echo '<fieldset><legend>' . __('Registration', true) . '</legend>';
    $registration_types = array(
        1 => __('Everyone may register', true),
        2 => __('No one may register', true)
        #3 => __('By invitation only', true)
    );
    echo $form->label('Network.registration_type', __('Type of registration policy for new users', true));
    echo $form->select('Network.registration_type', $registration_types, null, null, false);
    
    echo $form->input('Network.registration_restricted_hosts', array('label' => __('Restricted E-Mail hosts', true)));
    echo '<p>';
    __('If you want to restrict the email addresses of users that can register, define this setting. You can add more than one host by simply seperating them with spaces: <em>mycompany.com myothercompany.com</em>');
    echo '</p>';
    echo '</fieldset>';

    echo '<fieldset><legend>' . __('Misc', true) . '</legend>';
    echo $form->input('Network.use_ssl', array('label' => __('Use SSL', true)));    
    echo '<p>';
    __('When true, https (Port 443) is used for sensitive pages like /pages/login/ and /pages/register/. Make sure your webserver is configured for https!');
    echo '</p>';
    
    echo $form->input('Network.api_info_active', array('label' => __('Activate Info-API', true)));
    echo '<p>';
    __('If you want your server being displayed on <a href="http://noserub.com/sites/">noserub.com/sites</a> and display the number of your registered users there, please contact <a href="mailto:dirk.olbertz@gmail.com">dirk.olbertz@gmail.com</a> for listing on that page and enable this option.<br /><br />Beside the number of users on your site, this method also transfers the settings of your registration policy for new users to noserub.com. In addition, information about how up to date your installation is (ID of newest database migration) gathered, so we know which versions are used out there.<br /><br />We do not send the content of your restricted eMail hosts, but only the information, if this setting is used. If so, your site will be flagged as "not public" on <a href="http://noserub.com/sites/">noserub.com/sites/</a>.');
    echo '</p>';
    
    #echo $form->input('Network.allow_subscriptions', array('label' => __('Allow others to subscribe to this network')));
    echo '</fieldset>';
    
    echo $form->submit(__('Save', true));
    echo $form->submit(__('Cancel', true), array('name' => 'cancel'));
    echo $form->end(null);
} else { ?>
    <p>
        <?php __('You need to be logged in to the Admin area to see something here.'); ?>
    </p>
<?php }