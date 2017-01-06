# Geonames bundle
## Installation
**Install the bundle using composer:**

    composer require andvab/geonames-bundle

or add to composer.json

    {
        require: {
            "andvab/geonames-bundle": "*"
        }
    }

**Add the bundle to your AppKernel.php:**

    $bundles = array(
        // ...
        new Andvab\GeonamesBundle\AndvabGeonamesBundle(),
        // ...
    );

**Install or update database schema:**

    console doctrine:schema:update --force

## Commands:
**1) Clear the table "alternate_names".**

    php app/console andvab_geonames:clear:alternatenames [ru uk ...]
 In the params you can specify the list of languages for which it will delete, if not specified, the table will be cleared completely(faster). The list of languages can be viewed at the link - http://download.geonames.org/export/dump/iso-languagecodes.txt in the "Language" column

**2) Complete cleaning of the table "geonames"**

    php app/console andvab_geonames:clear:locations


**3) Update the data in the table "admin1_codes"** (Names for administrative division)

    php app/console andvab_geoname:update:admin1

**4) Update the data in the table "admin2_codes"** (Names for administrative subdivision)

    php app/console andvab_geoname:update:admin2

**5) Update the data in the table "alternate_names".**

    php app/console andvab_geoname:update:alternatenames [ru uk ...]
In the parameters you can specify the list of languages for which the update occurs if parameter is not specified, the table will be updated fully(longer). The list of languages can be viewed at the link - http://download.geonames.org/export/dump/iso-languagecodes.txt in the "Language" column

**6) Update the data in the table "countries".**

    php app/console andvab_geonames:update:countries


**7) Update the data in the table "geonames".**
    
    php app/console andvab_geonames:update:locations [ru ua ...]
In parameters, you can specify a list of countries for which the update occurs if parameter is not specified, the list will be taken from the table of countries and each country will be updated. The list of countries can be viewed at the link - http://download.geonames.org/export/dump/countryInfo.txt in the "iso" column

More information on the use of the tables and fields description tables can be viewed at the link - http://download.geonames.org/export/dump/ 