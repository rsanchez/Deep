# Deep

A set of Eloquent models for ExpressionEngine Channel Entries.

```
<?php

use rsanchez\Deep\Model\Entry;

$entries = Entry::channelName('blog')
        		->limit(1)
        		->show_future_entries()
                ->get();
?>

<?php foreach ($entries as $entry) : ?>
<article>
	<h1><?php echo $entry->title; ?></h1>
	
	<p class="date"><?php echo $entry->entry_date->format('F j, Y'); ?></p>

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