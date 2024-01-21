<?php
/**
 * src/config/database-encryption.php.
 *
 * @author      Austin Heap <me@austinheap.com>
 *
 * @version     v0.2.1
 */

return [

    /*
     * Enable database encryption.
     *
     * Default: false
     *
     * @var null|bool
     */
    'enabled' => true,

    /*
     * Prefix used in attribute header.
     *
     * Default: __LARAVEL-DATABASE-ENCRYPTED-%VERSION%__
     *
     * @var null|string
     */
    'prefix' => null,

    /*
     * Enable header versioning.
     *
     * Default: true
     *
     * @var null|bool
     */
    'versioning' => false,

    /*
     * Control characters used by header.
     *
     * Default: [
     *     'header' => [
     *         'start'      => 1, // or: chr(1)
     *         'stop'       => 4, // or: chr(4)
     *     ],
     *     'prefix' => [
     *         'start'      => 2, // or: chr(2)
     *         'stop'       => 3, // or: chr(3)
     *     ],
     *     'field'   => [
     *         'start'      => 30, // or: chr(30)
     *         'delimiter'  => 25, // or: chr(25)
     *         'stop'       => 23, // or: chr(23)
     *     ],
     * ]
     *
     * @var null|array
     */
    'control_characters' => null,

];
