<h1 align="center">Import de tableau excel dans le Framework Pods de Wordpress</h1>

Installation / Usage
--------------------

$WpPodsImporter = new Roxielfr\WpPodsImport\Controller\MainController();
```
$state = $WpPodsImporter->import('post',plugin_dir_path(__FILE__) . "import/", 'Products.xlsx',
    [
        'titre' => 
            [
                'field_type' => 'string',
                'field_name' => 'post_title',
            ],
        'description' => 
            [
                'field_type' => 'html',
                'field_name' => 'champ_html',
            ],
        'category' => 
            [
                'field_type' => 'taxonomy',
                'field_name' => 'champ_taxo',
                'taxonomy_name' => 'test_taxo',
            ],
        'image' => 
            [
                'field_type' => 'image',
                'field_name' => 'champ_image',
            ]
    ]
);
```
Requirements
------------

PHP 7.2.0 ou plus.

Authors
-------

- Baptiste Milot  | [GitHub](https://github.com/RoxielFr)

Security Reports
----------------

Merci d'envoyer un mail à [baptiste@roxiel.fr](mailto:baptiste@roxiel.fr). Merci!

License
-------

Wp Pods Import est en LICENCE PUBLIQUE RIEN À BRANLER- 



                LICENCE PUBLIQUE RIEN À BRANLER
                     Version 1, mars 2009

Copyright (C) 2009 Sam Hocevar
 14 rue de Plaisance, 75014 Paris, France

La copie et la distribution de copies exactes de cette licence sont
autorisées, et toute modification est permise à condition de changer
le nom de la licence.

        CONDITIONS DE COPIE, DISTRIBUTON ET MODIFICATION
              DE LA LICENCE PUBLIQUE RIEN À BRANLER

 0. Faites ce que vous voulez, j’en ai RIEN À BRANLER.


 --------------

            DO WHAT THE FUCK YOU WANT TO PUBLIC LICENSE
                   Version 2, December 2004

Copyright (C) 2004 Sam Hocevar <sam@hocevar.net>

Everyone is permitted to copy and distribute verbatim or modified
copies of this license document, and changing it is allowed as long
as the name is changed.

           DO WHAT THE FUCK YOU WANT TO PUBLIC LICENSE
  TERMS AND CONDITIONS FOR COPYING, DISTRIBUTION AND MODIFICATION

 0. You just DO WHAT THE FUCK YOU WANT TO.