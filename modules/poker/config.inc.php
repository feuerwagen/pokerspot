<?php
/**
* config file for one chispa module.
* It defines config params which won't be changed while site is online.
* Pre-definded sub arrays are:
*
* - actions: possible actions in the different site areas; if value is empty, this action is avaiable for all users
* - menu: menu items for the backend; array 'root' defines actions for the predefined root menu items
* - messages: error messages for form handling
* 		if one field needs more than one message (i.e. for mandantory check an the correct format)
* 		they will be used in the order of the checks (first: mandantory, second: format, third: special check)
* - rules: rules to check submitted values against; subarrays for each possible form action
*		possible keys for each value: mandantory (true, false), format (name of check method), check (method name:param)
*
* @version 0.1
*/

return array(
    // sub modules
    'sub' => array('poker_table', 'poker_spot'),
	// possible module actions
	'actions' => array(
		'form' => array(
            'bet' => '',
            'raise' => '',
            'call' => '',
            'fold' => '',
            'check' => '',
            'load' => '',
            'poll' => '', // poll for action at a poker table
            'join' => '', // join poker table
            'leave' => '', // leave poker table
            'chat' => '', // display chat messages
		),
		'backend' => array(
			'play' => '',
            'show' => '',
            'save' => '',
            'archive' => '',
		),
	),
	// menu items for the backend
	'menu' => array(
        /*'root' => array(
            'poker' => array(
                'action' => 'play'
            )
        ),//*/
        'poker' => array(
            array(
                'action' => 'play',
                'title' => 'Spielen',
                'priority' => 1
            ),
            array(
                'action' => 'archive',
                'title' => 'Archiv',
                'priority' => 5
            ),
        )
	),
    // messages for form error handling
    'messages' => array(
    ),
	// form rules
	'rules' => array(
        'bet' => array(
            'value' => array(
                'mandantory' => true,
                'format' => 'float',
            )
        ),
        'raise' => array(
            'value' => array(
                'mandantory' => true,
                'format' => 'float',
            )
        ),
        'call' => array(
            'value' => array(
                'mandantory' => true,
                'format' => 'float',
            )
        ),
        'fold' => array(),
        'check' => array(),
		'poll' => array(
            'timestamp' => array(
                'mandantory' => true,
                'format' => 'int',
            )
        ),
        'chat' => array(
            'timestamp' => array(
                'mandantory' => true,
                'format' => 'int',
            )
        ),
        'join' => array(
            'seat' => array(
                'mandantory' => true,
                'format' => 'int',
            ),
            'stack' => array(
                'mandantory' => true,
                'format' => 'int',
            ),
        ),
        'leave' => array(),
        'load' => array(),
    ),
);