<?php
/**
 * Home page content schema: sections → fields → defaults.
 *
 * The same array drives the admin form, the sanitizer and the templates,
 * so a field is defined exactly once. Defaults hold the approved design copy.
 *
 * Field types: text | textarea | url | email | image | number | checkbox | select | date | repeater | heading
 *
 * @package Bizmax
 */

defined( 'ABSPATH' ) || exit;

$feature_row = static function ( string $heading, string $sub, string $text, string $cta, string $image, string $flower ): array {
	return array(
		'enabled'   => array( 'type' => 'checkbox', 'label' => __( 'הצגת המקטע', 'bizmax' ), 'default' => true ),
		'heading'   => array( 'type' => 'text', 'label' => __( 'כותרת (H2)', 'bizmax' ), 'default' => $heading ),
		'flower'    => array(
			'type'    => 'select',
			'label'   => __( 'צבע הפרח ליד הכותרת', 'bizmax' ),
			'default' => $flower,
			'options' => array( 'blue' => __( 'כחול', 'bizmax' ), 'orange' => __( 'כתום', 'bizmax' ), 'green' => __( 'ירוק', 'bizmax' ) ),
		),
		'subheading' => array( 'type' => 'text', 'label' => __( 'כותרת משנה (H3)', 'bizmax' ), 'default' => $sub ),
		'text'      => array( 'type' => 'textarea', 'label' => __( 'פסקה', 'bizmax' ), 'default' => $text ),
		'cta_text'  => array( 'type' => 'text', 'label' => __( 'טקסט כפתור', 'bizmax' ), 'default' => $cta ),
		'cta_url'   => array( 'type' => 'url', 'label' => __( 'קישור כפתור', 'bizmax' ), 'default' => '#contact' ),
		'image'     => array( 'type' => 'image', 'label' => __( 'תמונה', 'bizmax' ), 'default' => 0, 'placeholder' => $image ),
		'image_alt' => array( 'type' => 'text', 'label' => __( 'טקסט חלופי לתמונה (alt)', 'bizmax' ), 'default' => $heading ),
	);
};

$capsule = static function ( string $text, string $url ): array {
	return array(
		'text' => array( 'type' => 'text', 'label' => __( 'טקסט', 'bizmax' ), 'default' => $text ),
		'url'  => array( 'type' => 'url', 'label' => __( 'קישור', 'bizmax' ), 'default' => $url ),
	);
};

return array(

	'hero' => array(
		'label'  => __( 'הירו', 'bizmax' ),
		'fields' => array(
			'title_1'   => array( 'type' => 'text', 'label' => __( 'כותרת – שורה 1', 'bizmax' ), 'default' => 'ביזמקס' ),
			'title_2'   => array( 'type' => 'text', 'label' => __( 'כותרת – שורה 2', 'bizmax' ), 'default' => 'הבית שבו' ),
			'highlight' => array( 'type' => 'text', 'label' => __( 'טקסט מודגש במרקר (המשך שורה 2)', 'bizmax' ), 'default' => 'יזמים צומחים!' ),
			'text'      => array(
				'type'    => 'textarea',
				'label'   => __( 'פסקה', 'bizmax' ),
				'default' => "ברוכים הבאים לביזמקס, המקום בו חזון פוגש יסודות מקצועיים, עסקים פוגשים את האנשים הנכונים, וחדשנות הופכת לעשייה שפורצת גבולות.\n\nכאן תמצאו את כל מה שצריך כדי להצליח – מרחבי עבודה מתקדמים, תוכניות ליווי עסקי, מסלולי יזמות מגוונים וקהילה תומכת של יזמים שמובילים שינוי. הצטרפו אלינו ותנו לעסק שלכם לפרוח!",
			),
			'cta_text'  => array( 'type' => 'text', 'label' => __( 'טקסט כפתור', 'bizmax' ), 'default' => 'הצטרפו אלינו עכשיו!' ),
			'cta_url'   => array( 'type' => 'url', 'label' => __( 'קישור כפתור', 'bizmax' ), 'default' => '#contact' ),
			'h_caps'    => array( 'type' => 'heading', 'label' => __( 'ארבע הקפסולות המרחפות', 'bizmax' ) ),
			'capsule_1' => array( 'type' => 'group', 'label' => __( 'קפסולה 1 (כהה, עם תמונה)', 'bizmax' ), 'fields' => $capsule( 'מתחם חללי עבודה משותפים', '#coworking' ) + array( 'image' => array( 'type' => 'image', 'label' => __( 'תמונה עגולה', 'bizmax' ), 'default' => 0, 'placeholder' => 'photo-office' ) ) ),
			'capsule_2' => array( 'type' => 'group', 'label' => __( 'קפסולה 2 (גרף)', 'bizmax' ), 'fields' => $capsule( 'מיטאפים והרצאות ליזמות וכישורים רכים', '#meetups' ) ),
			'capsule_3' => array( 'type' => 'group', 'label' => __( 'קפסולה 3 (ספירלה)', 'bizmax' ), 'fields' => array( 'title' => array( 'type' => 'text', 'label' => __( 'כותרת מודגשת', 'bizmax' ), 'default' => 'תכנית Bizlabs' ) ) + $capsule( 'הנבטה וצמיחה של סטארטאפים בבעלות חרדית', '#bizlabs' ) ),
			'capsule_4' => array( 'type' => 'group', 'label' => __( 'קפסולה 4 (גל)', 'bizmax' ), 'fields' => $capsule( 'בית ספר לעסקים ויזמות', '#deschool' ) ),
		),
	),

	'coworking' => array(
		'label'  => __( 'המתחם', 'bizmax' ),
		'anchor' => 'coworking',
		'fields' => $feature_row(
			'המתחם',
			"מקום לעבוד בו.\nאנשים לצמוח איתם.",
			"בסוף, עסק הוא גם אקסלים, לקוחות ומשימות אבל הוא קודם כל האנשים שאתה פוגש בדרך. במתחם ביזמקס בנינו סביבת עבודה שבה העבודה שלך פוגשת קהילה עסקית חיה — כזו שיכולה לפתוח דלת, לתת כיוון או פשוט לגרום לך לחשוב אחרת.\n\nכאן מחכים לך עמדות עבודה ומשרדים, חדרי ישיבות, מרחבים לפגישות, סביבת עבודה מקצועית וקהילה של יזמים ובעלי עסקים חרדים שנמצאים בדיוק כמוך בעשייה ובצמיחה.",
			'לתיאום סיור במתחם',
			'space-1',
			'blue'
		),
	),

	'deschool' => array(
		'label'  => __( 'דה-סקול', 'bizmax' ),
		'anchor' => 'deschool',
		'fields' => $feature_row(
			'דה-סקול - בית הספר לעסקים',
			'דה־סקול. בית ספר שמצמיח עסקים. בגדול.',
			"מסלול עומק לבעלי עסקים שרוצים לעשות את הקפיצה הבאה — לרכוש את הידע, הכלים והחשיבה הנדרשים כדי לעבור מניהול שוטף של העסק לבניית עסק גדול, יציב וצומח.\n\nבמשך התוכנית עובדים על כל תחומי הליבה של העסק — פיננסים, שיווק, מכירות, ניהול, עובדים ואסטרטגיה — לצד ליווי עסקי אישי, למידה ממומחים מובילים וחיבור לבעלי עסקים שנמצאים בדיוק באותה נקודת צמיחה.\n\nהמטרה היא לא רק לשפר את העסק של היום, אלא לבנות את העסק שאתה רוצה שיהיה לך מחר.",
			'הצמיחה שלך מתחילה כאן',
			'space-2',
			'orange'
		),
	),

	'bizlabs' => array(
		'label'  => __( 'ביזלאבס', 'bizmax' ),
		'anchor' => 'bizlabs',
		'fields' => $feature_row(
			'BizLabs – הבית ליזמות טכנולוגית חרדית',
			'מהניצוץ הראשון — לחברה שצומחת.',
			"BizLabs הוא האקוסיסטם שמלווה יזמים חרדים לאורך כל הדרך: ממודעות והשראה, דרך גיבוש והנבטת רעיון, לתוכניות האצה ועד ליווי חברות בצמיחה.\n\nבכל שלב מחכים לכם האנשים, הכלים, המנטורים והחיבורים שיכולים לקחת אותם לשלב הבא. מאז 2018 ליווינו עשרות יזמים וסטארטאפים חרדים — מרעיון ראשוני ועד חברות פעילות בשוק שגייסו מיליוני דולרים.\n\nארבעה שלבים. מטרה אחת: להצמיח את דור חברות הטכנולוגיה החרדיות הבא.",
			'רוצה עוד פרטים',
			'space-3',
			'blue'
		),
	),

	'meetups' => array(
		'label'  => __( 'מרחב צמיחה', 'bizmax' ),
		'anchor' => 'meetups',
		'fields' => array(
			'enabled'    => array( 'type' => 'checkbox', 'label' => __( 'הצגת המקטע', 'bizmax' ), 'default' => true ),
			'heading'    => array( 'type' => 'text', 'label' => __( 'כותרת (H2)', 'bizmax' ), 'default' => 'מרחב צמיחה' ),
			'subheading' => array( 'type' => 'text', 'label' => __( 'כותרת משנה (H3)', 'bizmax' ), 'default' => 'קורסים, הרצאות, מיטאפים ומפגשי העשרה — ארבע תחנות, מסלול אחד' ),
			'cta_text'   => array( 'type' => 'text', 'label' => __( 'טקסט כפתור', 'bizmax' ), 'default' => 'לכל הפעילויות' ),
			'cta_url'    => array( 'type' => 'url', 'label' => __( 'קישור כפתור', 'bizmax' ), 'default' => '#events' ),
			'stations'   => array(
				'type'    => 'repeater',
				'label'   => __( 'תחנות (מומלץ 4)', 'bizmax' ),
				'max'     => 6,
				'fields'  => array(
					'image' => array( 'type' => 'image', 'label' => __( 'תמונה עגולה', 'bizmax' ), 'default' => 0 ),
					'title' => array( 'type' => 'text', 'label' => __( 'כותרת', 'bizmax' ), 'default' => '' ),
					'text'  => array( 'type' => 'textarea', 'label' => __( 'תיאור', 'bizmax' ), 'default' => '', 'rows' => 3 ),
				),
				'default' => array(
					array( 'image' => 0, 'title' => 'קורסים מקצועיים', 'text' => 'כלים פרקטיים ולמידה מעמיקה לפיתוח, קידום וניהול המיזם שלכם.' ),
					array( 'image' => 0, 'title' => 'הרצאות', 'text' => 'ידע מעשי, טרנדים חמים ותוכן מעורר השראה ממיטב המומחים בתעשייה.' ),
					array( 'image' => 0, 'title' => 'אירועי קהילה', 'text' => 'המרחב האידיאלי לנטוורקינג, יצירת חיבורים, החלפת רעיונות ושיתופי פעולה.' ),
					array( 'image' => 0, 'title' => 'מפגשי גיבוש והעשרה', 'text' => 'סדנאות לפיתוח כישורים רכים, סיעורי מוחות וצמיחה אישית משותפת.' ),
				),
			),
		),
	),

	'events' => array(
		'label'  => __( 'יומן פעילות', 'bizmax' ),
		'anchor' => 'events',
		'fields' => array(
			'enabled'   => array( 'type' => 'checkbox', 'label' => __( 'הצגת המקטע', 'bizmax' ), 'default' => true ),
			'heading'   => array( 'type' => 'text', 'label' => __( 'כותרת (H2)', 'bizmax' ), 'default' => 'יומן פעילות' ),
			'hide_past' => array( 'type' => 'checkbox', 'label' => __( 'הסתרה אוטומטית של אירועים שתאריכם עבר', 'bizmax' ), 'default' => false ),
			'items'     => array(
				'type'    => 'repeater',
				'label'   => __( 'אירועים', 'bizmax' ),
				'max'     => 12,
				'fields'  => array(
					'image' => array( 'type' => 'image', 'label' => __( 'תמונה', 'bizmax' ), 'default' => 0 ),
					'date'  => array( 'type' => 'date', 'label' => __( 'תאריך', 'bizmax' ), 'default' => '' ),
					'meta'  => array( 'type' => 'text', 'label' => __( 'שעה ומקום', 'bizmax' ), 'default' => '19:30 · מרכז ביזמקס' ),
					'title' => array( 'type' => 'text', 'label' => __( 'שם האירוע', 'bizmax' ), 'default' => '' ),
					'text'  => array( 'type' => 'textarea', 'label' => __( 'תיאור קצר', 'bizmax' ), 'default' => '', 'rows' => 3 ),
					'url'   => array( 'type' => 'url', 'label' => __( 'קישור לאירוע', 'bizmax' ), 'default' => '#contact' ),
				),
				'default' => array(
					array( 'image' => 0, 'date' => gmdate( 'Y' ) . '-10-12', 'meta' => '19:30 · מרכז ביזמקס', 'title' => 'שם אירוע', 'text' => 'כאן יהיה טקסט קצר הסבר על האירוע', 'url' => '#contact' ),
					array( 'image' => 0, 'date' => gmdate( 'Y' ) . '-10-20', 'meta' => '19:30 · מרכז ביזמקס', 'title' => 'שם אירוע', 'text' => 'כאן יהיה טקסט קצר הסבר על האירוע', 'url' => '#contact' ),
					array( 'image' => 0, 'date' => gmdate( 'Y' ) . '-11-04', 'meta' => '19:30 · מרכז ביזמקס', 'title' => 'שם אירוע', 'text' => 'כאן יהיה טקסט קצר הסבר על האירוע', 'url' => '#contact' ),
					array( 'image' => 0, 'date' => gmdate( 'Y' ) . '-11-17', 'meta' => '19:30 · מרכז ביזמקס', 'title' => 'שם אירוע', 'text' => 'כאן יהיה טקסט קצר הסבר על האירוע', 'url' => '#contact' ),
				),
			),
		),
	),

	'about' => array(
		'label'  => __( 'אודות', 'bizmax' ),
		'anchor' => 'about',
		'fields' => array(
			'enabled'       => array( 'type' => 'checkbox', 'label' => __( 'הצגת המקטע', 'bizmax' ), 'default' => true ),
			'heading'       => array( 'type' => 'text', 'label' => __( 'כותרת (H2)', 'bizmax' ), 'default' => 'אודות ביזמקס' ),
			'intro'         => array( 'type' => 'textarea', 'label' => __( 'פסקת פתיחה', 'bizmax' ), 'default' => 'ביזמקס הוא מרכז חדשנות ועסקים בירושלים, הפועל לקידום יזמות, צמיחה עסקית וחדשנות בחברה החרדית — בית מקצועי, עסקי וקהילתי ליזמים, בעלי עסקים וחברות מהמגזר החרדי.' ),
			'h_card'        => array( 'type' => 'heading', 'label' => __( 'כרטיס התמונה הגבוה', 'bizmax' ) ),
			'card_image'    => array( 'type' => 'image', 'label' => __( 'תמונה', 'bizmax' ), 'default' => 0, 'placeholder' => 'photo-office' ),
			'card_badge'    => array( 'type' => 'text', 'label' => __( 'תגית', 'bizmax' ), 'default' => 'מאז 2017' ),
			'card_title'    => array( 'type' => 'text', 'label' => __( 'כותרת', 'bizmax' ), 'default' => 'הבית של היזמות החרדית בירושלים' ),
			'card_subtitle' => array( 'type' => 'text', 'label' => __( 'שורת משנה', 'bizmax' ), 'default' => 'הוקם על ידי קרן קמח, קרן אחים והרשות לפיתוח ירושלים, בשיתוף המשרד לירושלים ומסורת ישראל' ),
			'h_counters'    => array( 'type' => 'heading', 'label' => __( 'מונים', 'bizmax' ) ),
			'counter_1'     => array(
				'type'   => 'group',
				'label'  => __( 'מונה 1 (כהה)', 'bizmax' ),
				'fields' => array(
					'number' => array( 'type' => 'number', 'label' => __( 'מספר', 'bizmax' ), 'default' => 8000 ),
					'suffix' => array( 'type' => 'text', 'label' => __( 'סיומת (למשל +)', 'bizmax' ), 'default' => '+' ),
					'text'   => array( 'type' => 'text', 'label' => __( 'כיתוב', 'bizmax' ), 'default' => 'בעלי עסקים ויזמים חרדים נטלו חלק בפעילות מאז ההקמה' ),
				),
			),
			'counter_2'     => array(
				'type'   => 'group',
				'label'  => __( 'מונה 2 (בהיר)', 'bizmax' ),
				'fields' => array(
					'number' => array( 'type' => 'number', 'label' => __( 'מספר', 'bizmax' ), 'default' => 53 ),
					'suffix' => array( 'type' => 'text', 'label' => __( 'סיומת', 'bizmax' ), 'default' => '' ),
					'text'   => array( 'type' => 'text', 'label' => __( 'כיתוב', 'bizmax' ), 'default' => 'סטארטאפים לוו בביזלאבס בשבעה מחזורים — מרעיון ועד גיוסי מיליוני דולרים' ),
				),
			),
			'h_links'       => array( 'type' => 'heading', 'label' => __( 'כרטיסי קישור', 'bizmax' ) ),
			'link_1'        => array(
				'type'   => 'group',
				'label'  => __( 'כרטיס 1 (כובע אקדמי)', 'bizmax' ),
				'fields' => array(
					'title' => array( 'type' => 'text', 'label' => __( 'כותרת', 'bizmax' ), 'default' => 'The School' ),
					'text'  => array( 'type' => 'text', 'label' => __( 'טקסט', 'bizmax' ), 'default' => 'בית הספר לעסקים של ביזמקס — מעסק קטן לחברה גדולה, יציבה ומעסיקה.' ),
					'cta'   => array( 'type' => 'text', 'label' => __( 'טקסט קישור', 'bizmax' ), 'default' => 'להכיר את התוכנית' ),
					'url'   => array( 'type' => 'url', 'label' => __( 'קישור', 'bizmax' ), 'default' => '#deschool' ),
				),
			),
			'link_2'        => array(
				'type'   => 'group',
				'label'  => __( 'כרטיס 2 (רקטה)', 'bizmax' ),
				'fields' => array(
					'title' => array( 'type' => 'text', 'label' => __( 'כותרת', 'bizmax' ), 'default' => 'ביזלאבס' ),
					'text'  => array( 'type' => 'text', 'label' => __( 'טקסט', 'bizmax' ), 'default' => 'תוכנית האצה ליזמי טכנולוגיה — פלטפורמה מלאה לאורך כל צינור היזמות.' ),
					'cta'   => array( 'type' => 'text', 'label' => __( 'טקסט קישור', 'bizmax' ), 'default' => 'להכיר את התוכנית' ),
					'url'   => array( 'type' => 'url', 'label' => __( 'קישור', 'bizmax' ), 'default' => '#bizlabs' ),
				),
			),
			'closing'       => array( 'type' => 'textarea', 'label' => __( 'פסקת סיום (ממורכזת)', 'bizmax' ), 'default' => 'באמצעות ידע מקצועי, ליווי אישי, קשרים עסקיים, נטוורקינג, קהילה תומכת ונגישות למשקיעים ולמובילי תעשייה, ביזמקס פועל לצמיחתו של דור חדש של יזמים, בעלי עסקים וחברות טכנולוגיה מהחברה החרדית.' ),
		),
	),

	'alumni' => array(
		'label'  => __( 'הבוגרים מספרים', 'bizmax' ),
		'anchor' => 'alumni',
		'fields' => array(
			'enabled'  => array( 'type' => 'checkbox', 'label' => __( 'הצגת המקטע', 'bizmax' ), 'default' => true ),
			'heading'  => array( 'type' => 'text', 'label' => __( 'כותרת (H2)', 'bizmax' ), 'default' => 'הבוגרים מספרים' ),
			'subtitle' => array( 'type' => 'text', 'label' => __( 'שורת משנה', 'bizmax' ), 'default' => 'סיפורים אמיתיים של יזמים שצמחו כאן' ),
			'items'    => array(
				'type'    => 'repeater',
				'label'   => __( 'המלצות', 'bizmax' ),
				'max'     => 12,
				'fields'  => array(
					'image' => array( 'type' => 'image', 'label' => __( 'תמונת פרופיל', 'bizmax' ), 'default' => 0 ),
					'name'  => array( 'type' => 'text', 'label' => __( 'שם', 'bizmax' ), 'default' => '' ),
					'role'  => array( 'type' => 'text', 'label' => __( 'תפקיד / חברה', 'bizmax' ), 'default' => '' ),
					'text'  => array( 'type' => 'textarea', 'label' => __( 'המלצה מלאה (כרטיס גדול)', 'bizmax' ), 'default' => '', 'rows' => 4 ),
					'short' => array( 'type' => 'textarea', 'label' => __( 'גרסה קצרה (כרטיסים קטנים)', 'bizmax' ), 'default' => '', 'rows' => 2 ),
				),
				'default' => array(
					array( 'image' => 0, 'name' => 'יונתן הלר', 'role' => 'מנכ"ל ומייסד MikveTech', 'text' => 'כבעלים של סטארטאפ, אתה לא יכול להרשות לעצמך לצעוד לבד. אתה חייב סביבה שתתמוך בך, תאתגר אותך, ותיתן לך תחושת שייכות. בביזלאבס מצאתי בדיוק את זה – קהילה של יזמים עם שאיפות דומות, שמדברים באותה שפה, גם טכנולוגית וגם תרבותית. זה היה אחד הגורמים הכי משמעותיים שהחזיקו אותי בתמונה כשזה נהיה קשה.', 'short' => 'בביזלאבס מצאתי קהילה של יזמים עם שאיפות דומות, שמדברים באותה שפה — גם טכנולוגית וגם תרבותית.' ),
					array( 'image' => 0, 'name' => 'דני וייס', 'role' => 'מנכ"ל ושותף מייסד, GD Stride', 'text' => 'בדרך להצלחה, אחת הנקודות המשמעותיות עבורינו הייתה ההשתתפות בתוכנית של ביזלאבס. כתוכנית המיועדת ליזמים חרדים, היא העניקה לנו כלים, קשרים ותמיכה שסייעו לנו להתקדם בעולם היזמות.', 'short' => 'התוכנית העניקה לנו כלים, קשרים ותמיכה שסייעו לנו להתקדם בעולם היזמות.' ),
					array( 'image' => 0, 'name' => 'אלי ואלס', 'role' => 'מנכ"ל ושותף מייסד, MediMe', 'text' => 'בביזלאבס קיבלנו כלים מקצועיים לבניית המוצר, פיתוח אסטרטגי, ומיקוד שוק. אך התרומה לא מסתיימת בסיום התוכנית – כחלק ממעטפת התמיכה לבוגרים, אנו ממשיכים לקבל ליווי, ייעוץ, וחיבורים למשקיעים ושותפים אסטרטגיים.', 'short' => 'גם אחרי סיום התוכנית אנו ממשיכים לקבל ליווי, ייעוץ וחיבורים למשקיעים ושותפים אסטרטגיים.' ),
				),
			),
		),
	),

	'partners' => array(
		'label'  => __( 'השותפים המייסדים', 'bizmax' ),
		'anchor' => 'partners',
		'fields' => array(
			'enabled' => array( 'type' => 'checkbox', 'label' => __( 'הצגת המקטע', 'bizmax' ), 'default' => true ),
			'heading' => array( 'type' => 'text', 'label' => __( 'כותרת (H2)', 'bizmax' ), 'default' => 'השותפים המייסדים' ),
			'items'   => array(
				'type'    => 'repeater',
				'label'   => __( 'שותפים', 'bizmax' ),
				'max'     => 8,
				'fields'  => array(
					'logo' => array( 'type' => 'image', 'label' => __( 'לוגו', 'bizmax' ), 'default' => 0 ),
					'name' => array( 'type' => 'text', 'label' => __( 'שם', 'bizmax' ), 'default' => '' ),
					'text' => array( 'type' => 'textarea', 'label' => __( 'תיאור', 'bizmax' ), 'default' => '', 'rows' => 4 ),
					'url'  => array( 'type' => 'url', 'label' => __( 'קישור (אופציונלי)', 'bizmax' ), 'default' => '' ),
				),
				'default' => array(
					array( 'logo' => 0, 'name' => 'קרן קמ"ח', 'text' => 'מהגופים המובילים בישראל לקידום מקצועי ותעסוקתי של החברה החרדית. הקרן פועלת ליצירת הזדמנויות לצמיחה כלכלית, באמצעות הכשרה, השכלה, תעסוקה ויזמות המותאמות לצורכי הקהילה החרדית.', 'url' => '' ),
					array( 'logo' => 0, 'name' => 'אחים גלובל', 'text' => 'ארגון הפועל לקידום תעסוקה, עסקים ויזמות בחברה החרדית. הארגון מסייע ליזמים באמצעות סביבות עבודה מותאמות, ידע מקצועי, מנטורים, מקורות מימון, משקיעים וקשרים עסקיים בארץ ובעולם.', 'url' => '' ),
					array( 'logo' => 0, 'name' => 'הרשות לפיתוח ירושלים', 'text' => 'הזרוע המרכזית לקידום ופיתוח כלכלי של ירושלים, הפועלת לחיזוק מנועי הצמיחה של העיר וליצירת הזדמנויות חדשות לתעסוקה, עסקים וחדשנות. בין היתר, הרשות מקדמת תעסוקה מתקדמת, יזמות ומרכזי חדשנות בעיר.', 'url' => '' ),
					array( 'logo' => 0, 'name' => 'משרד ירושלים ומסורת ישראל', 'text' => 'המשרד הממשלתי האמון על קידום ופיתוח ירושלים, ובכלל זה חיזוק מנועי הצמיחה הכלכליים, התעסוקה וההשקעות בעיר. המשרד שותף בקידום תוכניות ויוזמות המחזקות את כלכלת ירושלים ואת אוכלוסיותיה השונות.', 'url' => '' ),
				),
			),
		),
	),

	'more' => array(
		'label'  => __( 'עוד בביזמקס + טופס', 'bizmax' ),
		'anchor' => 'more',
		'fields' => array(
			'enabled'    => array( 'type' => 'checkbox', 'label' => __( 'הצגת המקטע', 'bizmax' ), 'default' => true ),
			'heading'    => array( 'type' => 'text', 'label' => __( 'כותרת (H2)', 'bizmax' ), 'default' => 'עוד בביזמקס' ),
			'capsule_1'  => array( 'type' => 'group', 'label' => __( 'קפסולה 1 (כהה)', 'bizmax' ), 'fields' => $capsule( 'מתחם חללי עבודה משותפים', '#coworking' ) ),
			'capsule_2'  => array( 'type' => 'group', 'label' => __( 'קפסולה 2 (גרף)', 'bizmax' ), 'fields' => $capsule( 'מיטאפים והרצאות ליזמות וכישורים רכים', '#meetups' ) ),
			'capsule_3'  => array( 'type' => 'group', 'label' => __( 'קפסולה 3 (ירוקה)', 'bizmax' ), 'fields' => $capsule( 'בית ספר לעסקים ויזמות', '#deschool' ) ),
			'capsule_4'  => array( 'type' => 'group', 'label' => __( 'קפסולה 4 (צהובה)', 'bizmax' ), 'fields' => array( 'title' => array( 'type' => 'text', 'label' => __( 'כותרת מודגשת', 'bizmax' ), 'default' => 'תכנית Bizlabs' ) ) + $capsule( 'הנבטה וצמיחה של סטארטאפים בבעלות חרדית', '#bizlabs' ) ),
			'h_form'     => array( 'type' => 'heading', 'label' => __( 'טופס יצירת קשר', 'bizmax' ) ),
			'show_form'  => array( 'type' => 'checkbox', 'label' => __( 'הצגת הטופס', 'bizmax' ), 'default' => true ),
			'form_title' => array( 'type' => 'text', 'label' => __( 'כותרת הטופס', 'bizmax' ), 'default' => 'יצירת קשר' ),
			'form_btn'   => array( 'type' => 'text', 'label' => __( 'טקסט כפתור שליחה', 'bizmax' ), 'default' => 'שליחה' ),
			'form_subject' => array( 'type' => 'text', 'label' => __( 'נושא המייל שיישלח', 'bizmax' ), 'default' => 'פנייה חדשה מאתר ביזמקס' ),
		),
	),
);
