<?php

use Carbon_Fields\Container;
use Carbon_Fields\Field;

add_action('after_setup_theme', 'load_carbon_fields');
add_action('carbon_fields_register_fields', 'create_options_page');
function load_carbon_fields() {
    \Carbon_Fields\Carbon_Fields::boot();

}

function create_options_page(){
    Container::make( 'theme_options', __( 'Ajouter Joueur' ) )
    ->set_icon( 'dashicons-admin-users' )
    ->add_fields( array(
        Field::make( 'text', 'nom', __( 'Nom' ) )->set_attribute('placeholder', 'Nom'),
        Field::make( 'text', 'prenom', __( 'Prenom' ) )->set_attribute('placeholder', 'Prenom'),
        Field::make( 'date', 'date_naissance', __( 'Date de naissance' ) ),
        Field::make( 'text', 'adresse', __( 'Adresse' ) )->set_attribute('placeholder', 'Adresse'),
        Field::make( 'text', 'npa', __( 'NPA Lieu' ) )->set_attribute('placeholder', 'NPA Lieu'),
        Field::make( 'select', 'equipe', __( 'Equipe' ) )
            ->set_options( array(
                'U08B' => 'U08B',
                'U08A' =>'U08A',
                'U10B' =>'U10B',
                'U10A'=>'U10A',
                'U12B'=>'U12B',
                'U12A'=>'U12A',
                'U14M3'=>'U14M3',
                'U14M2'=>'U14M2',
                'U14M1'=>'U14M1',
                'U16M2'=>'U16M2',
                'U16M1'=>'U16M1',
                'U18M2'=>'U18M2',
                'U18M1'=>'U18M1',
                'U18U20M'=>'U18U20M',
                '2LCM'=>'2LCM',
                '1LNM'=>'1LNM'
            ) )
    ) );
}