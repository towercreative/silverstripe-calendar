<?php

namespace TitleDK\Calendar\Tags;

use SilverStripe\ORM\DataList;
use SilverStripe\ORM\DataObject;
use SilverStripe\View\Parsers\URLSegmentFilter;
use TitleDK\Calendar\Events\Event;

/**
 * A blog tag for keyword descriptions of a blog post.
 *
 * @property string $Title
 * @property string $Slug
 * @method \SilverStripe\ORM\ManyManyList|\TitleDK\Calendar\Events\Event[] Events()
 */
class EventTag extends DataObject
{
    //use BlogObject;

    /**
     * Use an exception code so that attempted writes can continue on
     * duplicate errors.
     *
     * @const string
     * This must be a string because ValidationException has decided we can't use int
     */
    const DUPLICATE_EXCEPTION = 'DUPLICATE';

    /**
     * {@inheritDoc}
     *
     * @var string
     */
    private static $table_name = 'EventTag';

    /**
     * @var array
     */
    private static $db = [
        'Title'      => 'Varchar(255)'
    ];

    /**
     * @var array
    // todo make tags per calendar
    private static $has_one = [
        'Calendar' => Calendar::class
    ];
     */

    /**
     * @var array
     */
    private static $many_many = [
        'Events' => Event::class
    ];

    /**
     * @todo is this needed?
     *
     * {@inheritdoc}
     */
    protected function getListUrlSegment()
    {
        return 'tag';
    }

    /**
     * {@inheritdoc}
     */
    protected function getDuplicateError()
    {
        return _t(__CLASS__ . '.Duplicate', 'A blog tag already exists with that name.');
    }


    /**
     * Looks for objects o the same type and the same value by the given Field
     *
     * @param  string $field E.g. URLSegment or Title
     * @return DataList
     */
    protected function getDuplicatesByField($field)
    {
        $duplicates = DataList::create(self::class)
            ->filter(
                [
                    $field   => $this->$field
                ]
            );

        if ($this->ID) {
            $duplicates = $duplicates->exclude('ID', $this->ID);
        }

        return $duplicates;
    }

}
