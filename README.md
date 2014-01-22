# Entries

**THIS IS A WORK IN PROGRESS, IT DOES NOT ACTUALLY WORK YET**

A pure PHP implementation of the ExpressionEngine {exp:channel:entries} tag.

```
<?php

use rsanchez\Entries\Channel;

$entries = Channel::entries()
		->channel('blog')
		->limit(1)
		->show_future_entries(true);
?>

<?php foreach ($entries as $entry) : ?>
<article>
	<h1><?php echo $entry->title; ?></h1>
	
	<p class="date"><?php echo $entry->entry_date('F j, Y'); ?></p>

	<?php echo $entry->description; ?>
</article>
<?php endforeach; ?>
```

##Todo

- autoloading (instead of bootstrap.php)
- composer
- unit tests
- fieldtype abstraction