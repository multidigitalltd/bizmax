<?php
/**
 * BizLabs page content schema: sections → fields → defaults (the approved design copy).
 *
 * Same field types as the home schema (see inc/home-content.php). Images that ship with the
 * theme are "asset" fields: they show until an image is chosen in the matching image field.
 *
 * @package Bizmax
 */

defined( 'ABSPATH' ) || exit;

$enabled = array(
	'type'    => 'checkbox',
	'label'   => __( 'הצגת המקטע', 'bizmax' ),
	'default' => true,
);

$heading = static function ( string $text, string $label = '' ): array {
	return array(
		'type'    => 'text',
		'label'   => '' !== $label ? $label : __( 'כותרת (H2)', 'bizmax' ),
		'default' => $text,
	);
};

$subtitle = static function ( string $text ): array {
	return array(
		'type'    => 'text',
		'label'   => __( 'כותרת משנה', 'bizmax' ),
		'default' => $text,
	);
};

$image = static function ( string $label = '' ): array {
	return array(
		'type'    => 'image',
		'label'   => '' !== $label ? $label : __( 'תמונה', 'bizmax' ),
		'default' => 0,
	);
};

$asset = array(
	'type'    => 'asset',
	'label'   => '',
	'default' => '',
);

return array(

	'header'   => array(
		'label'  => __( 'הידר', 'bizmax' ),
		'fields' => array(
			'logo'     => $image( __( 'לוגו ביזלאבס', 'bizmax' ) ) + array( 'placeholder' => 'bizlabs/logo-bizlabs' ),
			'logo_alt' => array(
				'type'    => 'text',
				'label'   => __( 'טקסט חלופי ללוגו', 'bizmax' ),
				'default' => 'BizLabs',
			),
			'links'    => array(
				'type'    => 'repeater',
				'label'   => __( 'קישורי התפריט בהידר של העמוד', 'bizmax' ),
				'max'     => 8,
				'fields'  => array(
					'text'   => array(
						'type'    => 'text',
						'label'   => __( 'טקסט', 'bizmax' ),
						'default' => '',
					),
					'url'    => array(
						'type'    => 'url',
						'label'   => __( 'קישור', 'bizmax' ),
						'default' => '',
					),
					'active' => array(
						'type'    => 'checkbox',
						'label'   => __( 'מודגש (העמוד הנוכחי)', 'bizmax' ),
						'default' => false,
					),
				),
				'default' => array(
					array(
						'text'   => 'אודות',
						'url'    => '#labout',
						'active' => false,
					),
					array(
						'text'   => 'תכניות ביזלאבס',
						'url'    => '#ltracks',
						'active' => true,
					),
					array(
						'text'   => 'יומן פעילות',
						'url'    => home_url( '/#events' ),
						'active' => false,
					),
					array(
						'text'   => 'פורטפוליו ביזלאבס',
						'url'    => '#lstories',
						'active' => false,
					),
					array(
						'text'   => 'יצירת קשר',
						'url'    => '#lform',
						'active' => false,
					),
				),
			),
			'cta'      => array(
				'type'    => 'checkbox',
				'label'   => __( 'כפתור צף "הירשמו עכשיו"', 'bizmax' ),
				'default' => true,
			),
			'cta_text' => array(
				'type'    => 'text',
				'label'   => __( 'טקסט הכפתור הצף', 'bizmax' ),
				'default' => 'הירשמו עכשיו',
			),
			'cta_url'  => array(
				'type'    => 'url',
				'label'   => __( 'קישור הכפתור הצף', 'bizmax' ),
				'default' => '#lform',
			),
		),
	),

	'hero'     => array(
		'label'  => __( 'הירו', 'bizmax' ),
		'fields' => array(
			'title_before' => array(
				'type'    => 'text',
				'label'   => __( 'כותרת – לפני המרקר', 'bizmax' ),
				'default' => 'בית',
			),
			'highlight'    => array(
				'type'    => 'text',
				'label'   => __( 'כותרת – טקסט במרקר', 'bizmax' ),
				'default' => 'ליזמות טכנולוגית',
			),
			'title_after'  => array(
				'type'    => 'text',
				'label'   => __( 'כותרת – אחרי המרקר', 'bizmax' ),
				'default' => 'חרדית',
			),
			'text'         => array(
				'type'    => 'textarea',
				'label'   => __( 'פסקה', 'bizmax' ),
				'default' => 'מאז 2018 אנחנו מלווים את הדור הבא של יזמי ההייטק החרדים – משלבי ההשראה והרעיון ועד שלבי היציאה לשוק והצמיחה. עד היום ליווינו עשרות יזמים ביניהם 36 בשלבים מתקדמים, אשר גייסו מימון בסך כולל של כ-62M$. אנחנו מאמינים שכל יזם ראוי להזדמנות – עם הכוונה, סביבה תומכת, וכלים מותאמים. BizLabs הוא האקוסיסטם שמאפשר לזה לקרות.',
			),
			'cta_text'     => array(
				'type'    => 'text',
				'label'   => __( 'טקסט כפתור', 'bizmax' ),
				'default' => 'הצטרפו אלינו עכשיו!',
			),
			'cta_url'      => array(
				'type'    => 'url',
				'label'   => __( 'קישור כפתור', 'bizmax' ),
				'default' => '#lform',
			),
			'image'        => $image() + array( 'placeholder' => 'bizlabs/hero' ),
			'image_alt'    => array(
				'type'    => 'text',
				'label'   => __( 'טקסט חלופי לתמונה (alt)', 'bizmax' ),
				'default' => 'נורה עם צמחייה על שולחן משרד',
			),
			'badge'        => array(
				'type'    => 'text',
				'label'   => __( 'תווית מתחת לתמונה', 'bizmax' ),
				'default' => 'כ-62M$ גויסו על ידי בוגרי התוכנית',
			),
		),
	),

	'tracks'   => array(
		'label'  => __( 'מסלולים', 'bizmax' ),
		'anchor' => 'ltracks',
		'fields' => array(
			'enabled'        => $enabled,
			'heading'        => $heading( 'המסלולים' ),
			'subtitle'       => $subtitle( 'ארבעה מסלולים שמלווים אתכם מהרעיון הראשון ועד הצמיחה' ),
			'kicker'         => array(
				'type'    => 'text',
				'label'   => __( 'המילה לפני המספר', 'bizmax' ),
				'default' => 'מסלול',
			),
			'audience_label' => array(
				'type'    => 'text',
				'label'   => __( 'כותרת "קהל יעד"', 'bizmax' ),
				'default' => 'קהל יעד',
			),
			'link_text'      => array(
				'type'    => 'text',
				'label'   => __( 'טקסט הקישור בכרטיס', 'bizmax' ),
				'default' => 'מעבר למסלול',
			),
			'items'          => array(
				'type'    => 'repeater',
				'label'   => __( 'מסלולים', 'bizmax' ),
				'max'     => 8,
				'fields'  => array(
					'title'    => array(
						'type'    => 'text',
						'label'   => __( 'שם המסלול', 'bizmax' ),
						'default' => '',
					),
					'english'  => array(
						'type'    => 'text',
						'label'   => __( 'שם באנגלית (אפור)', 'bizmax' ),
						'default' => '',
					),
					'tagline'  => array(
						'type'    => 'text',
						'label'   => __( 'סלוגן (ירוק)', 'bizmax' ),
						'default' => '',
					),
					'text'     => array(
						'type'    => 'textarea',
						'label'   => __( 'תיאור', 'bizmax' ),
						'default' => '',
						'rows'    => 4,
					),
					'audience' => array(
						'type'    => 'text',
						'label'   => __( 'קהל יעד', 'bizmax' ),
						'default' => '',
					),
					'url'      => array(
						'type'    => 'url',
						'label'   => __( 'קישור "מעבר למסלול"', 'bizmax' ),
						'default' => '#lform',
					),
				),
				'default' => array(
					array(
						'title'    => 'מודעות והשראה',
						'tagline'  => 'מיטאפים. קהילה. חדשנות',
						'text'     => '6 מפגשי ערב, 2 האקתונים ו-70 שעות ייעוץ פרטני, בליווי מנטורים בכירים ודמויות מפתח בתעשיית ההייטק',
						'audience' => 'אנשים בעלי זיקה טכנולוגית וראש עסקי – אין צורך ברעיון מוכן',
					),
					array(
						'title'    => 'הנבטה',
						'english'  => 'Pre-Accelerator',
						'text'     => '12 מפגשים לאורך שלושה חודשים הכוללים סדנאות בנושאי יזמות, ולידציה, שיווק, מיתוג וגיוס ראשוני, סיור מקצועי, מנטורינג אישי מבוגרים ודמו-דיי בפני משקיעים.',
						'audience' => 'יזמים עם רעיון ראשוני ומגובש לפני גיוס',
					),
					array(
						'title'    => 'האצה — אקסלרטור',
						'text'     => '15 מפגשים פרונטליים הכוללים סדנאות בנושאים מתקדמים ביזמות, שיווק, יציאה לשוק, הצגה בפני משקיעים וניהול פיננסי, שעות ייעוץ ומנטורינג אישי בסבסוד התוכנית, אירוע Showcase בפני משקיעים, מרחב עבודה ללא עלות בביזמקס ועוד.',
						'audience' => 'יזמים עם מוצר פעיל וצוות במשרה מלאה',
					),
					array(
						'title'    => 'מועדון המשקיעים',
						'english'  => 'BizLabs Investor Club',
						'text'     => '3 אירועי משקיעים בשנה, פגישות אחד-על-אחד מתואמות מראש וליווי בהכנה לפגישות השקעה ובבניית מצגות למשקיעים',
						'audience' => 'יזמים שגייסו מעל $150,000, עם צוות במשרה מלאה ומוצר בשלב מכירות',
					),
				),
			),
		),
	),

	'about'    => array(
		'label'  => __( 'אודות', 'bizmax' ),
		'anchor' => 'labout',
		'fields' => array(
			'enabled'         => $enabled,
			'heading'         => $heading( 'אודות ביזלאבס' ),
			'lead'            => array(
				'type'    => 'textarea',
				'label'   => __( 'פסקה ראשית', 'bizmax' ),
				'default' => 'BizLabs היא תוכנית הדגל של ביזמקס - מרכז החדשנות החרדי, המופעל על ידי קרן קמ"ח, קרן אחים והרשות לפיתוח ירושלים, בשיתוף משרד ירושלים ומסורת ישראל. התוכנית מצמיחה מיזמים טכנולוגיים בבעלות חרדית באמצעות ליווי, מנטורינג ולמידה מעמיקה של המקצועות והמיומנויות הנדרשים לבניית סטארט-אפ מצליח.',
			),
			'more'            => array(
				'type'    => 'textarea',
				'label'   => __( 'טקסט נוסף ("קרא עוד"; שורה ריקה = פסקה חדשה)', 'bizmax' ),
				'rows'    => 8,
				'default' => "BizLabs פועלת משנת 2018 מתוך מרכז החדשנות ביזמקס בירושלים, במטרה להגדיל את מספר ואיכות החברות הטכנולוגיות החרדיות הפועלות.\n\nהתוכנית החלה כאקסלרטור ליזמים לאחר גיוס ראשוני משמעותי. מתוך היכרות עם המשפך היזמי בחברה החרדית - שבו מעט יזמים מגיעים לשלב הקמת החברה, ומעטים עוד יותר מצליחים לצמוח - הורחבה הפעילות למודל של ארבעה שלבים משלימים: מודעות והשראה, הנבטה, האצה ומועדון המשקיעים. המודל מלווה יזמים, גברים ונשים, מהרעיון הראשון ועד למכירות, לצמיחה ולחיבור ישיר למקורות הון, בשילוב כלי בינה מלאכותית בכל אחד מהשלבים.",
			),
			'more_label'      => array(
				'type'    => 'text',
				'label'   => __( 'כפתור "קרא עוד"', 'bizmax' ),
				'default' => 'קרא עוד',
			),
			'less_label'      => array(
				'type'    => 'text',
				'label'   => __( 'כפתור "הצג פחות"', 'bizmax' ),
				'default' => 'הצג פחות',
			),
			'operators_label' => array(
				'type'    => 'text',
				'label'   => __( 'כותרת הלוגואים', 'bizmax' ),
				'default' => 'מופעל על ידי',
			),
			'operators'       => array(
				'type'    => 'repeater',
				'label'   => __( 'לוגואים של הגופים המפעילים', 'bizmax' ),
				'max'     => 10,
				'fields'  => array(
					'image' => $image( __( 'לוגו', 'bizmax' ) ),
					'asset' => $asset,
					'alt'   => array(
						'type'    => 'text',
						'label'   => __( 'שם הגוף (טקסט חלופי)', 'bizmax' ),
						'default' => '',
					),
				),
				'default' => array(
					array(
						'asset' => 'logo-kemach',
						'alt'   => 'קרן קמ"ח',
					),
					array(
						'asset' => 'logo-achim',
						'alt'   => 'קרן אחים',
					),
					array(
						'asset' => 'logo-jda',
						'alt'   => 'הרשות לפיתוח ירושלים',
					),
					array(
						'asset' => 'logo-jerusalem-heritage',
						'alt'   => 'מסורת ישראל',
					),
				),
			),
			'image'           => $image() + array( 'placeholder' => 'bizlabs/about' ),
			'image_alt'       => array(
				'type'    => 'text',
				'label'   => __( 'טקסט חלופי לתמונה (alt)', 'bizmax' ),
				'default' => 'מחזור ביזלאבס',
			),
			'badge_num'       => array(
				'type'    => 'text',
				'label'   => __( 'תווית – מספר', 'bizmax' ),
				'default' => '7',
			),
			'badge_title'     => array(
				'type'    => 'text',
				'label'   => __( 'תווית – כותרת', 'bizmax' ),
				'default' => 'מחזורים מאז 2018',
			),
			'badge_text'      => array(
				'type'    => 'text',
				'label'   => __( 'תווית – טקסט', 'bizmax' ),
				'default' => 'מרכז החדשנות ביזמקס, ירושלים',
			),
		),
	),

	'stats'    => array(
		'label'  => __( 'מספרים', 'bizmax' ),
		'anchor' => 'lstats',
		'fields' => array(
			'enabled' => $enabled,
			'heading' => $heading( 'ביזלאבס במספרים', __( 'כותרת קטנה', 'bizmax' ) ),
			'items'   => array(
				'type'    => 'repeater',
				'label'   => __( 'מספרים', 'bizmax' ),
				'max'     => 4,
				'fields'  => array(
					'number'    => array(
						'type'    => 'number',
						'label'   => __( 'מספר', 'bizmax' ),
						'default' => 0,
					),
					'suffix'    => array(
						'type'    => 'text',
						'label'   => __( 'סיומת (למשל M$)', 'bizmax' ),
						'default' => '',
					),
					'highlight' => array(
						'type'    => 'checkbox',
						'label'   => __( 'מספר ירוק', 'bizmax' ),
						'default' => false,
					),
					'label'     => array(
						'type'    => 'text',
						'label'   => __( 'תווית', 'bizmax' ),
						'default' => '',
					),
					'sub'       => array(
						'type'    => 'text',
						'label'   => __( 'טקסט קטן', 'bizmax' ),
						'default' => '',
					),
				),
				'default' => array(
					array(
						'number' => 7,
						'label'  => 'מחזורים מאז 2018',
					),
					array(
						'number' => 53,
						'label'  => 'חברות טכנולוגיות השתתפו',
						'sub'    => 'מתעשיות מגוונות',
					),
					array(
						'number'    => 62,
						'suffix'    => 'M$',
						'highlight' => true,
						'label'     => 'סך הון שגויס עד עתה',
						'sub'       => 'ממוצע של $1,178,020 לחברה',
					),
					array(
						'number' => 34,
						'label'  => 'חברות פעילות כיום',
						'sub'    => 'מתוך 53 שהשתתפו בתוכנית',
					),
				),
			),
		),
	),

	'team'     => array(
		'label'  => __( 'צוות', 'bizmax' ),
		'anchor' => 'lteam',
		'fields' => array(
			'enabled'  => $enabled,
			'heading'  => $heading( 'צוות התוכנית' ),
			'subtitle' => $subtitle( 'האנשים שמלווים אתכם לאורך הדרך' ),
			'members'  => array(
				'type'    => 'repeater',
				'label'   => __( 'אנשי צוות (ללא תמונה מוצגות ראשי התיבות)', 'bizmax' ),
				'max'     => 18,
				'fields'  => array(
					'image' => $image( __( 'תמונה', 'bizmax' ) ),
					'name'  => array(
						'type'    => 'text',
						'label'   => __( 'שם', 'bizmax' ),
						'default' => '',
					),
					'role'  => array(
						'type'    => 'text',
						'label'   => __( 'תפקיד', 'bizmax' ),
						'default' => '',
					),
				),
				'default' => array(
					array(
						'name' => 'אליהו דינוביץ',
						'role' => 'מנכ"ל ביזלאבס',
					),
					array(
						'name' => 'יהודה ריבנוביץ',
						'role' => 'צוות התוכנית',
					),
					array(
						'name' => 'איציק קרומבי',
						'role' => 'מייסד ויועץ תוכן',
					),
					array(
						'name' => 'חיה ניסלביץ',
						'role' => 'מנהלת המחקר ותהליכי מדידה',
					),
					array(
						'name' => 'נתי יעל',
						'role' => 'מנהל פעילות ביזמקס',
					),
					array(
						'name' => 'חני רייכמן',
						'role' => 'צוות התוכנית',
					),
				),
			),
		),
	),

	'board'    => array(
		'label'  => __( 'בורד תעשייה', 'bizmax' ),
		'anchor' => 'lboard',
		'fields' => array(
			'enabled' => $enabled,
			'heading' => $heading( 'בורד תעשייה' ),
			'members' => array(
				'type'    => 'repeater',
				'label'   => __( 'חברי הבורד (שם, תפקיד וחברה באנגלית)', 'bizmax' ),
				'max'     => 18,
				'fields'  => array(
					'image'   => $image( __( 'תמונה (לאורך, 3:4)', 'bizmax' ) ),
					'name'    => array(
						'type'    => 'text',
						'label'   => __( 'שם', 'bizmax' ),
						'default' => '',
					),
					'title'   => array(
						'type'    => 'text',
						'label'   => __( 'תפקיד', 'bizmax' ),
						'default' => '',
					),
					'company' => array(
						'type'    => 'text',
						'label'   => __( 'חברה', 'bizmax' ),
						'default' => '',
					),
				),
				'default' => array(
					array(
						'name'    => 'Erel Margalit',
						'title'   => 'Founder and Chairman',
						'company' => 'Jerusalem Venture Partners',
					),
					array(
						'name'    => 'David Bloom',
						'title'   => 'Partner',
						'company' => 'Noé Group',
					),
					array(
						'name'    => 'Dr. Anat Angel',
						'title'   => 'Director General',
						'company' => 'Wolfson Medical Center',
					),
					array(
						'name'    => 'Marc Schimmel',
						'title'   => 'Founder & President, Angel investor',
						'company' => 'Achim Global Foundation',
					),
					array(
						'name'    => 'Miri Yoskowitz',
						'title'   => 'Audit Partner',
						'company' => 'KPMG',
					),
					array(
						'name'    => 'Kobi Samboursky',
						'title'   => 'Founder & Managing Partner',
						'company' => 'Glilot Capital Partners',
					),
					array(
						'name'    => 'Amit Keren',
						'title'   => 'Managing Director',
						'company' => 'Deutsche Telekom Israel',
					),
					array(
						'name'    => 'Harold Wiener',
						'title'   => 'General Partner',
						'company' => 'Terra Venture Partners',
					),
					array(
						'name'    => 'Hadar Avtalion',
						'title'   => 'Startups, Accelerator & Incubator BD',
						'company' => 'AWS',
					),
				),
			),
		),
	),

	'stories'  => array(
		'label'  => __( 'סיפורי הצלחה', 'bizmax' ),
		'anchor' => 'lstories',
		'fields' => array(
			'enabled'      => $enabled,
			'heading'      => $heading( 'הבוגרים שלנו - סיפורי הצלחה' ),
			'subtitle'     => $subtitle( 'חברות שיצאו מהתוכנית ופועלות היום בשווקים בינלאומיים' ),
			'former_label' => array(
				'type'    => 'text',
				'label'   => __( 'המילה לפני השם הקודם', 'bizmax' ),
				'default' => 'לשעבר',
			),
			'items'        => array(
				'type'    => 'repeater',
				'label'   => __( 'חברות', 'bizmax' ),
				'max'     => 12,
				'fields'  => array(
					'name'     => array(
						'type'    => 'text',
						'label'   => __( 'שם החברה', 'bizmax' ),
						'default' => '',
					),
					'former'   => array(
						'type'    => 'text',
						'label'   => __( 'שם קודם (לא חובה)', 'bizmax' ),
						'default' => '',
					),
					'founders' => array(
						'type'    => 'text',
						'label'   => __( 'מייסדים', 'bizmax' ),
						'default' => '',
					),
					'text'     => array(
						'type'    => 'textarea',
						'label'   => __( 'תיאור', 'bizmax' ),
						'default' => '',
						'rows'    => 4,
					),
					'logo'     => $image( __( 'לוגו', 'bizmax' ) ),
					'asset'    => $asset,
				),
				'default' => array(
					array(
						'name'     => 'Botanohealth',
						'founders' => "איתמר חייקין, יניב קיטרון, שי ז'אן",
						'text'     => 'Botanohealth פיתחה דרך טבעית להגן על יבולים ממחלות פטרייתיות, המבוססת על מולקולות המופקות מהצמח עצמו. הטכנולוגיה עברה בהצלחה את התהליך הרגולטורי בארצות הברית - הישג משמעותי בתחום האגריטק - והחברה פועלת היום בחמישה שווקים: ארה"ב, הפיליפינים, מקסיקו, קולומביה ואקוודור.',
						'asset'    => 'bizlabs/story-botanohealth',
					),
					array(
						'name'     => 'MikvaTech',
						'former'   => 'Pool Purity',
						'founders' => 'יונתן הלר',
						'text'     => 'יונתן הלר פיתח טכנולוגיה לחיטוי וחמצון מים בדרך טבעית, ללא כימיקלים. הפתרון, המוגן בפטנט, החל את דרכו בשוק המקומי והתרחב משם לשווקים בינלאומיים. MikvaTech פועלת היום בארצות הברית, בישראל ובעשרות מדינות נוספות.',
						'asset'    => 'bizlabs/story-mikvatech',
					),
					array(
						'name'     => 'Healables',
						'founders' => 'משה לבוביץ, דני מוסקוביץ',
						'text'     => 'Healables פיתחה מכשיר לביש להקלה על כאב ללא תרופות, המשלב גלי מיקרו-אלקטרודה ובינה מלאכותית המתאימה את הטיפול למשתמש. המוצר מיועד לספורטאים ולמתמודדים עם כאב כרוני, והחברה הצליחה להביא חומרה רפואית לשוק בשלוש מדינות - ישראל, ארצות הברית ויפן.',
						'asset'    => 'bizlabs/story-healables',
					),
					array(
						'name'     => 'SelfCAD',
						'founders' => 'יצחק א. ברויאר',
						'text'     => 'יצחק ברויאר בנה תוכנת עיצוב תלת-ממד מלאה, נגישה במחירה ופשוטה דיה כדי להתחיל לעבוד בה מיד. התוכנה נמכרת ישירות למשתמשים ברחבי העולם והגיעה לפולין, לארצות הברית, להודו ולישראל - מוצר עולמי שנכתב מירושלים.',
						'asset'    => 'bizlabs/story-selfcad',
					),
					array(
						'name'     => 'Novotalk',
						'founders' => 'אברהם מרדכי שיינפלד',
						'text'     => 'Novotalk מציעה טיפול בגמגום כשירות טלרפואה, המבוסס על תרגול יומיומי בסביבה אמיתית ולא על פגישות קליניות בלבד. המודל מרחיב את נגישות הטיפול ומאפשר לצוותים קליניים ללוות מטופלים רבים יותר. החברה פועלת בישראל ובארצות הברית.',
						'asset'    => 'bizlabs/story-novotalk',
					),
					array(
						'name'     => 'BSQ',
						'former'   => 'Conext Global',
						'founders' => 'נדב פוליטי, אור שביט',
						'text'     => 'BSQ פיתחה מערכת חיישני IoT המותקנת בתוך מערבל הבטון ומעבירה נתוני איכות לענן בזמן אמת. במקום בדיקות מדגמיות שתוצאותיהן מגיעות בדיעבד, מקבל האתר בקרה רציפה לאורך כל היציקה. החברה פועלת בישראל ובשווקים בינלאומיים.',
						'asset'    => 'bizlabs/story-bsq',
					),
				),
			),
		),
	),

	'faq'      => array(
		'label'  => __( 'שאלות נפוצות', 'bizmax' ),
		'anchor' => 'lfaq',
		'fields' => array(
			'enabled'  => $enabled,
			'heading'  => $heading( 'שאלות נפוצות' ),
			'subtitle' => $subtitle( 'כל מה שחשוב לדעת לפני ההרשמה' ),
			'cta_text' => array(
				'type'    => 'text',
				'label'   => __( 'טקסט הכפתור', 'bizmax' ),
				'default' => 'לא מצאתם תשובה? דברו איתנו',
			),
			'cta_url'  => array(
				'type'    => 'url',
				'label'   => __( 'קישור הכפתור', 'bizmax' ),
				'default' => '#lform',
			),
			'items'    => array(
				'type'    => 'repeater',
				'label'   => __( 'שאלות (מתחלקות לשתי עמודות)', 'bizmax' ),
				'max'     => 20,
				'fields'  => array(
					'question' => array(
						'type'    => 'text',
						'label'   => __( 'שאלה', 'bizmax' ),
						'default' => '',
					),
					'answer'   => array(
						'type'    => 'textarea',
						'label'   => __( 'תשובה (שורה ריקה = פסקה חדשה)', 'bizmax' ),
						'default' => '',
						'rows'    => 4,
					),
				),
				'default' => array(
					array(
						'question' => 'למי מיועדות תוכניות BizLabs?',
						'answer'   => 'לגברים ונשים מהחברה החרדית המעוניינים להקים או לפתח סטארטאפ טכנולוגי. יש לנו מסלולים מותאמים למיזמים הנמצאים בשלבים שונים – החל מהשראה ורעיונאות, ועד כאלו עם פעילות גלובלית בינלאומית.',
					),
					array(
						'question' => 'האם צריך רקע טכנולוגי כדי להשתתף?',
						'answer'   => 'לא בהכרח. אנחנו מקבלים גם יזמים בעלי ניסיון עסקי או רעיון חזק, בתנאי שיש להם כוונה לבנות מוצר טכנולוגי ולגייס צוות מתאים.',
					),
					array(
						'question' => 'מה ההבדל בין פרה-אקסלרטור לאקסלרטור?',
						'answer'   => "<b>פרה-אקסלרטור</b> – מסלול בן 12 מפגשים שמתאים ליזמים עם רעיון בשלבי התחלה אשר מעוניינים לבדוק היתכנות ולבנות בסיס עסקי יציב.\n\n<b>אקסלרטור</b> – תוכנית מתקדמת לסטארטאפים פעילים, הכוללת ייעוץ אישי, פיתוח עסקי, הכנה לגיוס כספים וחשיפה בפני משקיעים.",
					),
					array(
						'question' => 'האם התוכניות בתשלום?',
						'answer'   => 'ההשתתפות בתוכניות מסובסדת בחלקה או במלואה, בהתאם למסלול. בפרה-אקסלרטור ובאקסלרטור, לדוגמה, יש דמי רצינות סמליים.',
					),
					array(
						'question' => 'איפה מתקיימים המפגשים?',
						'answer'   => 'רוב המפגשים מתקיימים בירושלים, במתחם ביזמקס (הצבי 15). לעיתים מתקיימות פעילויות גם בבני ברק או בסיורים חיצוניים לפי הצורך.',
					),
					array(
						'question' => 'איך מתקבלים לתוכנית?',
						'answer'   => 'יש למלא טופס הרשמה קצר. מועמדים מתאימים יזומנו לריאיון אישי מול מנהלי התוכנית. בחלק מהמסלולים מתקיימת גם ועדת קבלה מקצועית.',
					),
					array(
						'question' => 'אילו תחומים מתאימים לתוכנית?',
						'answer'   => 'אנחנו פתוחים לכל רעיון שיש לו פוטנציאל להפוך לסטארטאפ טכנולוגי – אפליקציות, פתרונות בריאות, חינוך, פיננסים, שיווק, בינה מלאכותית ועוד.',
					),
					array(
						'question' => 'יש תמיכה גם אחרי סיום התוכנית?',
						'answer'   => 'בהחלט. בוגרי האקסלרטור מצטרפים לקהילת BizLabs Scale – רשת בוגרים מקצועית עם אירועים, מנטורינג וקשרים בתעשייה.',
					),
				),
			),
		),
	),

	'data'     => array(
		'label'  => __( 'נתונים וגרפים', 'bizmax' ),
		'anchor' => 'linfo',
		'fields' => array(
			'enabled'     => $enabled,
			'heading'     => $heading( 'קצת על חרדים וסטארטאפים' ),

			'h_tab1'      => array(
				'type'  => 'heading',
				'label' => __( 'לשונית 1 – תחומי הסטארטאפים (עוגה)', 'bizmax' ),
			),
			'tab1'        => array(
				'type'    => 'text',
				'label'   => __( 'שם הלשונית', 'bizmax' ),
				'default' => 'אקסלרטור ביזלאבס',
			),
			'industry'    => array(
				'type'    => 'repeater',
				'label'   => __( 'פלחי העוגה (אחוזים)', 'bizmax' ),
				'max'     => 16,
				'fields'  => array(
					'label' => array(
						'type'    => 'text',
						'label'   => __( 'תחום', 'bizmax' ),
						'default' => '',
					),
					'value' => array(
						'type'    => 'decimal',
						'label'   => __( 'אחוז', 'bizmax' ),
						'default' => 0,
					),
					'color' => array(
						'type'    => 'color',
						'label'   => __( 'צבע', 'bizmax' ),
						'default' => '#1B3764',
					),
				),
				'default' => array(
					array(
						'label' => 'MedTech',
						'value' => 24,
						'color' => '#3b5f9e',
					),
					array(
						'label' => 'others',
						'value' => 20,
						'color' => '#1b3764',
					),
					array(
						'label' => 'App development',
						'value' => 10,
						'color' => '#2fd68a',
					),
					array(
						'label' => 'CleanTech',
						'value' => 7,
						'color' => '#0f2447',
					),
					array(
						'label' => 'EdTech',
						'value' => 7,
						'color' => '#6f8fc0',
					),
					array(
						'label' => 'HR-Tech',
						'value' => 5,
						'color' => '#2b3f63',
					),
					array(
						'label' => 'MarTech',
						'value' => 5,
						'color' => '#44536f',
					),
					array(
						'label' => 'FinTech',
						'value' => 2,
						'color' => '#8fa8cf',
					),
					array(
						'label' => 'BioTech',
						'value' => 2,
						'color' => '#d8dee9',
					),
					array(
						'label' => '3D',
						'value' => 2,
						'color' => '#1e7a4f',
					),
					array(
						'label' => 'AgroTech',
						'value' => 2,
						'color' => '#4fbf8a',
					),
				),
			),
			'block_1'     => array(
				'type'    => 'text',
				'label'   => __( 'קוביה 1 (כחולה)', 'bizmax' ),
				'default' => '7 מחזורים מאז 2018',
			),
			'block_2'     => array(
				'type'    => 'text',
				'label'   => __( 'קוביה 2 (ירוקה)', 'bizmax' ),
				'default' => '53 סטארטאפים מתעשיות מגוונות',
			),
			'block_3'     => array(
				'type'    => 'text',
				'label'   => __( 'קוביה 3 (לבנה)', 'bizmax' ),
				'default' => '34 חברות פעילות כיום',
			),

			'h_tab2'      => array(
				'type'  => 'heading',
				'label' => __( 'לשונית 2 – גיוס הון (עמודות)', 'bizmax' ),
			),
			'tab2'        => array(
				'type'    => 'text',
				'label'   => __( 'שם הלשונית', 'bizmax' ),
				'default' => 'גיוס הון',
			),
			'bars_title'  => array(
				'type'    => 'text',
				'label'   => __( 'כותרת הגרף', 'bizmax' ),
				'default' => 'התפלגות שכיחויות סך הון שגויס',
			),
			'bars_unit'   => array(
				'type'    => 'text',
				'label'   => __( 'יחידות', 'bizmax' ),
				'default' => '(באלפי $)',
			),
			'bars'        => array(
				'type'    => 'repeater',
				'label'   => __( 'עמודות', 'bizmax' ),
				'max'     => 12,
				'fields'  => array(
					'value' => array(
						'type'    => 'number',
						'label'   => __( 'מספר חברות', 'bizmax' ),
						'default' => 0,
					),
					'label' => array(
						'type'    => 'text',
						'label'   => __( 'טווח', 'bizmax' ),
						'default' => '',
					),
				),
				'default' => array(
					array(
						'value' => 5,
						'label' => '0–50',
					),
					array(
						'value' => 7,
						'label' => '51–100',
					),
					array(
						'value' => 14,
						'label' => '101–300',
					),
					array(
						'value' => 10,
						'label' => '301–1,000',
					),
					array(
						'value' => 6,
						'label' => '1,100–5,000',
					),
					array(
						'value' => 3,
						'label' => '5,100–10,000',
					),
				),
			),
			'total_value' => array(
				'type'    => 'text',
				'label'   => __( 'סכום כולל', 'bizmax' ),
				'default' => '$62,435,080',
			),
			'total_label' => array(
				'type'    => 'text',
				'label'   => __( 'תווית הסכום הכולל', 'bizmax' ),
				'default' => 'סך הון שגויס עד עתה',
			),
			'avg_value'   => array(
				'type'    => 'text',
				'label'   => __( 'ממוצע', 'bizmax' ),
				'default' => '$1,178,020',
			),
			'avg_label'   => array(
				'type'    => 'text',
				'label'   => __( 'תווית הממוצע', 'bizmax' ),
				'default' => 'ממוצע לחברה',
			),

			'h_tab3'      => array(
				'type'  => 'heading',
				'label' => __( 'לשונית 3 – פוטנציאל (עוגה ונתונים)', 'bizmax' ),
			),
			'tab3'        => array(
				'type'    => 'text',
				'label'   => __( 'שם הלשונית', 'bizmax' ),
				'default' => 'פוטנציאל ליזמות טכנולוגית',
			),
			'potential'   => array(
				'type'    => 'repeater',
				'label'   => __( 'פלחי העוגה (אחוזים)', 'bizmax' ),
				'max'     => 12,
				'fields'  => array(
					'label' => array(
						'type'    => 'text',
						'label'   => __( 'קבוצה', 'bizmax' ),
						'default' => '',
					),
					'value' => array(
						'type'    => 'decimal',
						'label'   => __( 'אחוז', 'bizmax' ),
						'default' => 0,
					),
					'color' => array(
						'type'    => 'color',
						'label'   => __( 'צבע', 'bizmax' ),
						'default' => '#1B3764',
					),
				),
				'default' => array(
					array(
						'label' => 'עובדות',
						'value' => 50,
						'color' => '#1e7a4f',
					),
					array(
						'label' => 'עובדים',
						'value' => 21.7,
						'color' => '#1b3764',
					),
					array(
						'label' => 'תלמידות סמינר',
						'value' => 15.7,
						'color' => '#2b6cb0',
					),
					array(
						'label' => 'הכשרות וסטודנטים',
						'value' => 7,
						'color' => '#0f2447',
					),
					array(
						'label' => 'סטודנטיות',
						'value' => 2.9,
						'color' => '#4fbf8a',
					),
					array(
						'label' => 'אחר',
						'value' => 1.7,
						'color' => '#38bdf8',
					),
				),
			),
			'pot_1'       => array(
				'type'    => 'text',
				'label'   => __( 'נתון 1 (ירוק)', 'bizmax' ),
				'default' => 'כ-11,500 נשים עובדות',
			),
			'pot_1_note'  => array(
				'type'    => 'text',
				'label'   => __( 'נתון 1 – הערה', 'bizmax' ),
				'default' => '',
			),
			'pot_2'       => array(
				'type'    => 'text',
				'label'   => __( 'נתון 2 (ירוק)', 'bizmax' ),
				'default' => 'כ-4,500 תלמידות',
			),
			'pot_2_note'  => array(
				'type'    => 'text',
				'label'   => __( 'נתון 2 – הערה', 'bizmax' ),
				'default' => '(ברגע נתון)',
			),
			'pot_3'       => array(
				'type'    => 'text',
				'label'   => __( 'נתון 3 (כחול)', 'bizmax' ),
				'default' => 'כ-5,000 עובדים גברים',
			),
			'pot_3_note'  => array(
				'type'    => 'text',
				'label'   => __( 'נתון 3 – הערה', 'bizmax' ),
				'default' => '',
			),
			'pot_4'       => array(
				'type'    => 'text',
				'label'   => __( 'נתון 4 (כחול)', 'bizmax' ),
				'default' => 'כ-2,000 תלמידים גברים',
			),
			'pot_4_note'  => array(
				'type'    => 'text',
				'label'   => __( 'נתון 4 – הערה', 'bizmax' ),
				'default' => '(ברגע נתון)',
			),

			'h_tab4'      => array(
				'type'  => 'heading',
				'label' => __( 'לשונית 4 – מודל המשפך (מלמעלה למטה)', 'bizmax' ),
			),
			'tab4'        => array(
				'type'    => 'text',
				'label'   => __( 'שם הלשונית', 'bizmax' ),
				'default' => 'מודל המשפך של ביזלאבס',
			),
			'funnel'      => array(
				'type'    => 'repeater',
				'label'   => __( 'שלבי המשפך (4 שלבים, מהצר לרחב)', 'bizmax' ),
				'max'     => 4,
				'fields'  => array(
					'title' => array(
						'type'    => 'text',
						'label'   => __( 'שלב', 'bizmax' ),
						'default' => '',
					),
					'text'  => array(
						'type'    => 'text',
						'label'   => __( 'תיאור', 'bizmax' ),
						'default' => '',
					),
				),
				'default' => array(
					array(
						'title' => 'מועדון המשקיעים',
						'text'  => 'חיבור ישיר למשקיעים, פגישות אחד-על-אחד וליווי בהכנה לגיוס',
					),
					array(
						'title' => 'האצה',
						'text'  => 'תוכנית מקצועית לסטארטאפים עם צוות פעיל ומוצר טכנולוגי',
					),
					array(
						'title' => 'הנבטה',
						'text'  => 'ביסוס הרעיון, ולידציה ובניית תוכנית עסקית',
					),
					array(
						'title' => 'מודעות והשראה',
						'text'  => 'מיטאפים, קהילה וחדשנות — לקהל הרחב, ללא צורך ברעיון מוכן',
					),
				),
			),
		),
	),

	'partners' => array(
		'label'  => __( 'שותפי תעשייה', 'bizmax' ),
		'anchor' => 'lpartners',
		'fields' => array(
			'enabled' => $enabled,
			'heading' => $heading( 'שותפי תעשייה' ),
			'logos'   => array(
				'type'    => 'repeater',
				'label'   => __( 'לוגואים', 'bizmax' ),
				'max'     => 30,
				'fields'  => array(
					'image' => $image( __( 'לוגו', 'bizmax' ) ),
					'asset' => $asset,
					'alt'   => array(
						'type'    => 'text',
						'label'   => __( 'שם (טקסט חלופי)', 'bizmax' ),
						'default' => '',
					),
					'url'   => array(
						'type'    => 'url',
						'label'   => __( 'קישור (לא חובה)', 'bizmax' ),
						'default' => '',
					),
				),
				'default' => array(
					array(
						'asset' => 'bizlabs/partner-google',
						'alt'   => 'Google',
					),
					array(
						'asset' => 'bizlabs/partner-aws',
						'alt'   => 'AWS',
					),
					array(
						'asset' => 'bizlabs/partner-poalim',
						'alt'   => 'Poalim Hi-Tech',
					),
					array(
						'asset' => 'bizlabs/partner-microsoft',
						'alt'   => 'Microsoft',
					),
					array(
						'asset' => 'bizlabs/partner-research',
						'alt'   => 'רשות מחקר וחדשנות',
					),
					array(
						'asset' => 'bizlabs/partner-kpmg',
						'alt'   => 'KPMG',
					),
					array(
						'asset' => 'bizlabs/partner-yigal-arnon',
						'alt'   => 'Yigal Arnon & Co.',
					),
					array(
						'asset' => 'bizlabs/partner-jvp',
						'alt'   => 'JVP',
					),
					array(
						'asset' => 'bizlabs/partner-iati',
						'alt'   => 'IATI – Israel Advanced Technology Industries',
					),
					array(
						'asset' => 'bizlabs/partner-fiverr',
						'alt'   => 'Fiverr',
					),
				),
			),
		),
	),

	'form'     => array(
		'label'  => __( 'טופס הרשמה', 'bizmax' ),
		'anchor' => 'lform',
		'fields' => array(
			'enabled'      => $enabled,
			'heading'      => $heading( 'מוכנים לקחת את הרעיון שלב קדימה?' ),
			'text'         => array(
				'type'    => 'textarea',
				'label'   => __( 'טקסט', 'bizmax' ),
				'default' => 'השאירו פרטים ונחזור אליכם עם המסלול המתאים ביותר לשלב שבו אתם נמצאים.',
				'rows'    => 3,
			),
			'points'       => array(
				'type'    => 'repeater',
				'label'   => __( 'נקודות ממוספרות', 'bizmax' ),
				'max'     => 8,
				'fields'  => array(
					'text' => array(
						'type'    => 'text',
						'label'   => __( 'טקסט', 'bizmax' ),
						'default' => '',
					),
				),
				'default' => array(
					array( 'text' => 'שיחת אבחון ללא עלות' ),
					array( 'text' => 'התאמת מסלול לשלב שלכם' ),
					array( 'text' => 'מנטורים מתעשיית ההייטק' ),
					array( 'text' => 'חיבור לקהילת הבוגרים' ),
				),
			),
			'show_contact' => array(
				'type'    => 'checkbox',
				'label'   => __( 'הצגת טלפון ומייל (מהגדרות "פרטי קשר ופוטר")', 'bizmax' ),
				'default' => true,
			),
			'btn'          => array(
				'type'    => 'text',
				'label'   => __( 'טקסט כפתור השליחה', 'bizmax' ),
				'default' => 'שליחה',
			),
			'subject'      => array(
				'type'    => 'text',
				'label'   => __( 'נושא המייל שנשלח אליכם', 'bizmax' ),
				'default' => 'פנייה חדשה מעמוד ביזלאבס',
			),
		),
	),

	'more'     => array(
		'label'  => __( 'עוד בביזמקס', 'bizmax' ),
		'anchor' => 'lmore',
		'fields' => array(
			'enabled'   => $enabled,
			'heading'   => $heading( 'עוד בביזמקס' ),
			'intro'     => array(
				'type'   => 'group',
				'label'  => __( 'הקפסולה הגדולה', 'bizmax' ),
				'fields' => array(
					'title' => array(
						'type'    => 'text',
						'label'   => __( 'כותרת', 'bizmax' ),
						'default' => 'הכירו את ביזמקס',
					),
					'text'  => array(
						'type'    => 'textarea',
						'label'   => __( 'טקסט', 'bizmax' ),
						'default' => 'מרכז החדשנות החרדי בירושלים — קהילה, מרחבי עבודה, מיטאפים ותוכניות יזמות תחת קורת גג אחת.',
						'rows'    => 3,
					),
					'link'  => array(
						'type'    => 'text',
						'label'   => __( 'טקסט הקישור', 'bizmax' ),
						'default' => 'לדף הבית של ביזמקס',
					),
					'url'   => array(
						'type'    => 'url',
						'label'   => __( 'קישור', 'bizmax' ),
						'default' => home_url( '/' ),
					),
				),
			),
			'capsule_1' => array(
				'type'   => 'group',
				'label'  => __( 'קפסולה 1 (כהה)', 'bizmax' ),
				'fields' => array(
					'text' => array(
						'type'    => 'text',
						'label'   => __( 'טקסט', 'bizmax' ),
						'default' => 'מתחם חללי עבודה משותפים',
					),
					'url'  => array(
						'type'    => 'url',
						'label'   => __( 'קישור', 'bizmax' ),
						'default' => home_url( '/#coworking' ),
					),
				),
			),
			'capsule_2' => array(
				'type'   => 'group',
				'label'  => __( 'קפסולה 2 (גרף)', 'bizmax' ),
				'fields' => array(
					'text' => array(
						'type'    => 'text',
						'label'   => __( 'טקסט', 'bizmax' ),
						'default' => 'מיטאפים והרצאות ליזמות וכישורים רכים',
					),
					'url'  => array(
						'type'    => 'url',
						'label'   => __( 'קישור', 'bizmax' ),
						'default' => home_url( '/#meetups' ),
					),
				),
			),
			'capsule_3' => array(
				'type'   => 'group',
				'label'  => __( 'קפסולה 3 (ירוקה)', 'bizmax' ),
				'fields' => array(
					'text' => array(
						'type'    => 'text',
						'label'   => __( 'טקסט', 'bizmax' ),
						'default' => 'בית ספר לעסקים ויזמות',
					),
					'url'  => array(
						'type'    => 'url',
						'label'   => __( 'קישור', 'bizmax' ),
						'default' => home_url( '/theschool/' ),
					),
				),
			),
			'capsule_4' => array(
				'type'   => 'group',
				'label'  => __( 'קפסולה 4 (ביזלאבס)', 'bizmax' ),
				'fields' => array(
					'title' => array(
						'type'    => 'text',
						'label'   => __( 'כותרת מודגשת', 'bizmax' ),
						'default' => 'תכנית Bizlabs',
					),
					'text'  => array(
						'type'    => 'text',
						'label'   => __( 'טקסט', 'bizmax' ),
						'default' => 'הנבטה וצמיחה של סטארטאפים בבעלות חרדית',
					),
					'url'   => array(
						'type'    => 'url',
						'label'   => __( 'קישור', 'bizmax' ),
						'default' => '#lhero',
					),
				),
			),
		),
	),
);
