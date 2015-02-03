<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Model;

use Illuminate\Database\Eloquent\Builder;
use rsanchez\Deep\Hydrator\HydratorCollection;
use rsanchez\Deep\Hydrator\DehydratorCollection;
use rsanchez\Deep\Repository\ChannelRepository;
use rsanchez\Deep\Repository\SiteRepository;
use rsanchez\Deep\Collection\TitleCollection;
use rsanchez\Deep\Collection\AbstractTitleCollection;
use rsanchez\Deep\Collection\AbstractModelCollection;
use rsanchez\Deep\Collection\PropertyCollection;
use rsanchez\Deep\Hydrator\HydratorFactory;
use rsanchez\Deep\Hydrator\DehydratorInterface;
use rsanchez\Deep\Relations\HasOneFromRepository;
use rsanchez\Deep\Validation\ValidatableInterface;
use rsanchez\Deep\Validation\Factory as ValidatorFactory;
use rsanchez\Deep\Model\StringableInterface;
use rsanchez\Deep\Model\PropertyInterface;
use Carbon\Carbon;
use Closure;
use DateTime;

/**
 * Model for the channel_titles table
 */
class Title extends AbstractEntity
{
    use JoinableTrait, GlobalAttributeVisibilityTrait, HasChannelRepositoryTrait, HasSiteRepositoryTrait;

}
