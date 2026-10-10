<?php
/**
 * Accessibility statement ("הצהרת נגישות") page content.
 *
 * The text follows the statement of Kivun (kivun.org.il/accessibility), which works from the
 * same building, with the brand changed to Bizmax, Bizmax's accessibility contact, and the list
 * of on-screen adjustments matching the theme's own accessibility panel.
 *
 * The page is created (or a theme placeholder draft is filled) once and published. After that
 * it is an ordinary page: editors change it in the page editor, and the theme never overwrites
 * a page someone has edited.
 *
 * @package Bizmax
 */

defined( 'ABSPATH' ) || exit;

/**
 * Accessibility contact details.
 *
 * @return array{name:string,role:string,phone:string}
 */
function bizmax_a11y_contact(): array {
	/**
	 * Filter the accessibility contact printed in the statement.
	 *
	 * @param array{name:string,role:string,phone:string} $contact Contact.
	 */
	return (array) apply_filters(
		'bizmax_a11y_contact',
		array(
			'name'  => 'יהודה',
			'role'  => 'אחראי נגישות',
			'phone' => '054-8138076',
		)
	);
}

/**
 * Block markup helpers for the statement (so the page opens as normal blocks in the editor).
 *
 * @param string $html  Inner HTML (already escaped).
 * @param int    $level Heading level.
 */
function bizmax_block_heading( string $html, int $level = 2 ): string {
	$attrs = 2 === $level ? '' : ' {"level":' . $level . '}';
	return sprintf( "<!-- wp:heading%1\$s -->\n<h%2\$d class=\"wp-block-heading\">%3\$s</h%2\$d>\n<!-- /wp:heading -->\n\n", $attrs, $level, $html );
}

/**
 * Paragraph block.
 *
 * @param string $html Inner HTML (already escaped).
 */
function bizmax_block_paragraph( string $html ): string {
	return "<!-- wp:paragraph -->\n<p>" . $html . "</p>\n<!-- /wp:paragraph -->\n\n";
}

/**
 * List block.
 *
 * @param string[] $items Item HTML (already escaped).
 */
function bizmax_block_list( array $items ): string {
	$out = "<!-- wp:list -->\n<ul class=\"wp-block-list\">";
	foreach ( $items as $item ) {
		$out .= "<!-- wp:list-item -->\n<li>" . $item . "</li>\n<!-- /wp:list-item -->";
	}
	return $out . "</ul>\n<!-- /wp:list -->\n\n";
}

/**
 * A phone number as an accessible tel: link.
 *
 * @param string $phone Phone as displayed.
 */
function bizmax_tel_link( string $phone ): string {
	$digits = preg_replace( '/[^\d+]/', '', $phone );
	return sprintf( '<a href="tel:%1$s">%2$s</a>', esc_attr( (string) $digits ), esc_html( $phone ) );
}

/**
 * The statement content (block markup).
 */
function bizmax_a11y_statement_content(): string {
	$contact = bizmax_a11y_contact();
	$phone   = (string) bizmax_mod( 'bizmax_phone' );
	$email   = sanitize_email( (string) bizmax_mod( 'bizmax_email' ) );
	$address = 'רחוב הצבי 15, בניין בזק, ירושלים';

	$c  = bizmax_block_paragraph( 'ביזמקס רואה חשיבות רבה במתן שירות שוויוני, מכובד, נגיש ומקצועי לכלל הציבור, לרבות אנשים עם מוגבלות.' );
	$c .= bizmax_block_paragraph( 'אנו פועלים לקידום הנגישות באתר האינטרנט ובשירותים הניתנים על ידינו, בהתאם להוראות חוק שוויון זכויות לאנשים עם מוגבלות, התשנ"ח–1998, ולתקנות שוויון זכויות לאנשים עם מוגבלות (התאמות נגישות לשירות), התשע"ג–2013.' );

	$c .= bizmax_block_heading( 'נגישות אתר האינטרנט' );
	$c .= bizmax_block_paragraph( 'האתר נועד לאפשר לכלל המשתמשים, ובכלל זה משתמשים עם מוגבלות, לקבל מידע על שירותי ביזמקס, קורסים, סדנאות, תוכניות ליזמים ואירועים.' );
	$c .= bizmax_block_paragraph( 'האתר הותאם, ככל האפשר, להוראות התקן הישראלי ת"י 5568 לנגישות תכנים באינטרנט, ברמת AA, המבוסס על הנחיות WCAG 2.0 של ארגון W3C.' );
	$c .= bizmax_block_paragraph( 'מתאפשר ניווט באתר באמצעות מקלדת, שימוש בטכנולוגיות מסייעות, מבנה כותרות תקין, טקסט חלופי לתמונות והתאמת האתר לתצוגה במכשירים ניידים.' );

	$c .= bizmax_block_heading( 'התאמות הנגישות באתר', 3 );
	$c .= bizmax_block_paragraph( 'באתר קיים תפריט נגישות המאפשר, בין היתר:' );
	$c .= bizmax_block_list(
		array(
			'הגדלת הטקסט עד פי שניים;',
			'ניגודיות גבוהה;',
			'רקע בהיר;',
			'מעבר לתצוגת גווני אפור;',
			'שימוש בגופן קריא;',
			'הדגשת כותרות;',
			'הדגשת קישורים והוספת קו תחתון לקישורים;',
			'הגדלת הריווח בין שורות, מילים ואותיות;',
			'עצירת אנימציות;',
			'הגדלת סמן העכבר;',
			'הדגשת מיקוד המקלדת;',
			'איפוס הגדרות הנגישות.',
		)
	);

	$c .= bizmax_block_heading( 'סייגים לנגישות', 3 );
	$c .= bizmax_block_paragraph( 'אנו ממשיכים לפעול לשיפור נגישות האתר כחלק ממחויבותנו לאפשר שימוש נוח ונגיש לכלל הציבור.' );
	$c .= bizmax_block_paragraph( 'למרות מאמצינו, ייתכן שיימצאו באתר עמודים, מסמכים, קבצים, סרטונים או רכיבים שמקורם בצדדים שלישיים אשר טרם הונגשו באופן מלא. ככל שנתקלתם בתוכן שאינו נגיש, ניתן לפנות אלינו, ואנו נעשה מאמץ סביר לספק את המידע בדרך נגישה ובהתאם לנסיבות.' );

	$c .= bizmax_block_heading( 'הסדרי נגישות במרכז' );
	$c .= bizmax_block_paragraph( 'ביזמקס מקבל קהל ב' . esc_html( $address ) . '.' );
	$c .= bizmax_block_paragraph( 'הסדרי הנגישות הקיימים במקום כוללים:' );
	$c .= bizmax_block_list(
		array(
			'לא קיימת בבניין חניה, ניתן לחנות ברחוב (בתשלום עירוני);',
			'קיימת גישה נגישה מהרחוב לכניסה לבניין;',
			'הכניסה למבנה נגישה;',
			'קיימת מעלית נגישה לקומה 1;',
			'קיימים שירותי נכים בשתי הקומות;',
			'קיימת עמדת שירות נגישה;',
			'לא קיימת מערכת עזר לשמיעה;',
			'מותרת כניסה עם חיית שירות.',
		)
	);
	$c .= bizmax_block_paragraph( 'לקבלת מידע נוסף בדבר הסדרי הנגישות במקום או לתיאום התאמות נגישות מראש, ניתן לפנות אלינו באמצעות פרטי ההתקשרות המפורטים להלן.' );

	$c .= bizmax_block_heading( 'פנייה בנושא נגישות' );
	$c .= bizmax_block_paragraph( 'נתקלתם בבעיית נגישות באתר או בשירות? נשמח לקבל את פנייתכם כדי שנוכל לבדוק את הבעיה ולפעול לתיקונה.' );
	$c .= bizmax_block_paragraph( 'בפנייה מומלץ לציין:' );
	$c .= bizmax_block_list(
		array(
			'תיאור הבעיה שבה נתקלתם;',
			'העמוד או השירות שבו התעוררה הבעיה;',
			'סוג הדפדפן והמכשיר שבהם השתמשתם;',
			'הטכנולוגיה המסייעת שבה נעשה שימוש, ככל שנעשה;',
			'פרטי קשר לצורך קבלת מענה.',
		)
	);

	$c      .= bizmax_block_heading( 'פרטי רכז הנגישות' );
	$c      .= bizmax_block_paragraph(
		'שם: ' . esc_html( $contact['name'] ) . '<br>'
		. 'תפקיד: ' . esc_html( $contact['role'] ) . '<br>'
		. 'טלפון ישיר: ' . bizmax_tel_link( $contact['phone'] )
	);
	$general = 'ניתן לפנות גם לביזמקס';
	if ( '' !== trim( $phone ) ) {
		$general .= ' בטלפון: ' . bizmax_tel_link( $phone );
	}
	if ( is_email( $email ) ) {
		$general .= ( '' !== trim( $phone ) ? ' או' : '' ) . ' בדואר אלקטרוני: ' . sprintf( '<a href="mailto:%1$s">%1$s</a>', esc_html( antispambot( $email ) ) );
	}
	$c .= bizmax_block_paragraph( $general . '.' );
	$c .= bizmax_block_paragraph( 'כתובת לקבלת קהל: ' . esc_html( $address ) . '.' );

	$c .= bizmax_block_heading( 'עדכון הצהרת הנגישות' );
	$c .= bizmax_block_paragraph( 'הצהרת הנגישות עודכנה לאחרונה ביום: ' . esc_html( wp_date( 'j.n.Y' ) ) );
	$c .= bizmax_block_paragraph( 'בביזמקס נמשיך לפעול לשיפור הנגישות באתר ובשירותיו, ונעדכן הצהרה זו בהתאם לשינויים ולהתאמות שיבוצעו.' );

	return trim( $c );
}

/**
 * Whether page content is empty or still the placeholder draft created by earlier theme versions.
 *
 * @param string $content Page content.
 */
function bizmax_a11y_statement_is_placeholder( string $content ): bool {
	if ( '' === trim( wp_strip_all_tags( $content ) ) ) {
		return true;
	}
	return str_contains( $content, '[שם]' ) && str_contains( $content, '[תאריך]' );
}

/**
 * The page that holds the statement: the one chosen in the Customizer, else the theme's slug.
 */
function bizmax_a11y_statement_page(): ?WP_Post {
	$id   = absint( bizmax_mod( 'bizmax_a11y_page' ) );
	$page = $id ? get_post( $id ) : get_page_by_path( 'accessibility-statement' );
	return ( $page instanceof WP_Post && 'page' === $page->post_type && 'trash' !== $page->post_status ) ? $page : null;
}

/**
 * Create and publish the statement, or fill and publish the theme's placeholder draft.
 * A page whose content someone edited is never touched.
 *
 * @return string 'created', 'filled' or 'kept'.
 */
function bizmax_ensure_a11y_page(): string {
	$page = bizmax_a11y_statement_page();

	if ( ! $page ) {
		$id = wp_insert_post(
			array(
				'post_type'    => 'page',
				'post_status'  => 'publish',
				'post_title'   => 'הצהרת נגישות',
				'post_name'    => 'accessibility-statement',
				'post_content' => bizmax_a11y_statement_content(),
			),
			true
		);
		return is_wp_error( $id ) ? 'kept' : 'created';
	}

	if ( ! bizmax_a11y_statement_is_placeholder( (string) $page->post_content ) ) {
		return 'kept';
	}

	$result = wp_update_post(
		array(
			'ID'           => $page->ID,
			'post_status'  => 'publish',
			'post_content' => bizmax_a11y_statement_content(),
		),
		true
	);
	return is_wp_error( $result ) ? 'kept' : 'filled';
}

/**
 * Sites that already run the theme: fill in the statement once, on the next admin visit.
 */
function bizmax_maybe_upgrade_a11y_statement(): void {
	if ( (int) get_option( 'bizmax_a11y_statement_setup', 0 ) >= 1 || ! current_user_can( 'edit_theme_options' ) || ! current_user_can( 'publish_pages' ) ) {
		return;
	}
	update_option( 'bizmax_a11y_statement_setup', 1, false );
	bizmax_ensure_a11y_page();
}
add_action( 'admin_init', 'bizmax_maybe_upgrade_a11y_statement' );
