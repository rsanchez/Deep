# Entries

**THIS IS A WORK IN PROGRESS, IT DOES NOT ACTUALLY WORK YET**

A pure PHP implementation of the ExpressionEngine {exp:channel:entries} tag.

```
<?php

use rsanchez\Deep\Deep;

$entries = Deep::entries()
		->channel('blog')
		->limit(1)
		->show_future_entries();
?>

<?php foreach ($entries as $entry) : ?>
<article>
	<h1><?php echo $entry->title; ?></h1>
	
	<p class="date"><?php echo $entry->entry_date('F j, Y'); ?></p>

	<?php echo $entry->description; ?>
</article>
<?php endforeach; ?>
```

## Installation

Add this to your `composer.json`:

    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/rsanchez/Deep"
        }
    ],
    "minimum-stability": "dev",
    "require": {
        "rsanchez/deep": "dev-develop"
    }

Make sure you load composer's autoloader at the top of your `config.php` (your actual vendor path may vary):

    require_once FCPATH.'vendor/autoload.php';


##Todo

- autoloading
- composer
- unit tests
- fieldtype abstraction