<?php
/**
 * Local delivery city data — one entry per priority city.
 *
 * Each city carries GENUINELY UNIQUE content (intro, neighborhoods, ZIPs, a
 * distinct local angle, occasions and FAQs). The template (template-city-
 * delivery.php) is shared, but the copy differs meaningfully per city — this is
 * the difference between helpful local landing pages and thin "doorway" clones
 * that Google filters. Keep it that way: never ship two cities with the same
 * paragraphs and a swapped name.
 *
 * @package Wildflower
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * All city landing pages, keyed by page slug.
 *
 * @return array<string, array<string, mixed>>
 */
function wildflower_delivery_cities() {
	$cities = array(

		'flower-delivery-boston' => array(
			'name'      => 'Boston',
			'title'     => 'Same-Day Flower Delivery in Boston, MA | Wildflower',
			'metadesc'  => 'Hand-tied, farm-fresh bouquets delivered same-day across Boston — Back Bay, Beacon Hill, the South End, Seaport and beyond. Order by 1 PM.',
			'answer'    => 'Yes — Wildflower delivers hand-tied, farm-fresh flowers same-day across Boston. Order by 1 PM ET and a local courier brings your bouquet to the door the same afternoon, from Back Bay and Beacon Hill to the Seaport, South End and Jamaica Plain.',
			'lead'      => 'We are a Boston flower studio, so the city is home turf. Whether it is a last-minute apology to an apartment on Newbury Street, a get-well arrangement to Mass General, or a birthday to a triple-decker in JP, we hand-tie it the same morning and deliver it upright and hydrated, often within a few hours.',
			'zips'      => array( '02108', '02109', '02110', '02111', '02113', '02114', '02115', '02116', '02118', '02119', '02120', '02121', '02122', '02124', '02125', '02127', '02128', '02129', '02130', '02131', '02132', '02134', '02135', '02199', '02210', '02215' ),
			'areas'     => array( 'Back Bay', 'Beacon Hill', 'South End', 'North End', 'Seaport', 'Fenway', 'Charlestown', 'Jamaica Plain', 'Dorchester', 'Roxbury', 'West Roxbury', 'Brighton', 'Allston', 'Roslindale', 'Downtown', 'South Boston' ),
			'fee'       => 'Boston is our closest zone — delivery starts at $19, and it is a flat $15 on orders of $85+.',
			'angle_t'   => 'The whole city, one studio',
			'angle_b'   => 'Because we are based here, Boston addresses get our fastest turnarounds and latest cut-off. We know the buildings with front desks, the JP walk-ups, and which Seaport towers need a call on arrival — the small things that get flowers to the right hands, not left in a lobby.',
			'occasions' => array(
				array( 'Hospital & get-well', 'Same-day arrangements to Mass General, Tufts and Boston Medical — bright, low-scent and vase-ready.' ),
				array( 'Apologies & romance', 'A bouquet on Newbury or Beacon Hill within hours, when the moment cannot wait until tomorrow.' ),
				array( 'New-home & housewarming', 'Welcome a friend to their first Southie or JP apartment with something that makes it feel like home.' ),
			),
			'faqs'      => array(
				array( 'How fast can you deliver flowers in Boston?', 'Order by 1 PM ET and we deliver the same afternoon across Boston. Many central deliveries (Back Bay, South End, Downtown) arrive within a few hours.' ),
				array( 'Which Boston neighborhoods do you cover?', 'All of them — Back Bay, Beacon Hill, the South End, North End, Seaport, Fenway, Charlestown, Jamaica Plain, Dorchester, Roxbury, Brighton, Allston, South Boston and more.' ),
				array( 'Can you deliver to a Boston hospital or office?', 'Yes. We deliver to hospitals, offices and hotels daily — just add the recipient’s floor, unit or front-desk details at checkout and we handle the rest.' ),
			),
		),

		'flower-delivery-cambridge' => array(
			'name'      => 'Cambridge',
			'title'     => 'Same-Day Flower Delivery in Cambridge, MA | Wildflower',
			'metadesc'  => 'Fresh flower delivery across Cambridge — Harvard Square, Kendall, Central & Porter. Same-day by 1 PM. Campus, lab and office deliveries welcome.',
			'answer'    => 'Yes — Wildflower hand-delivers fresh flowers same-day throughout Cambridge, from Harvard and Central Square to the Kendall Square biotech corridor and Porter. Order by 1 PM ET for same-day.',
			'lead'      => 'Cambridge sits in our closest delivery zone, just over the river. It is a city of campuses and labs, so we do a lot of graduation bouquets to Harvard Yard and MIT, and standing office orders to Kendall Square startups — alongside the everyday birthdays and thank-yous to the leafy streets off Brattle and Mass Ave.',
			'zips'      => array( '02138', '02139', '02140', '02141', '02142' ),
			'areas'     => array( 'Harvard Square', 'Central Square', 'Kendall Square', 'Porter Square', 'East Cambridge', 'Cambridgeport', 'North Cambridge', 'Inman Square', 'Mid-Cambridge', 'Riverside' ),
			'fee'       => 'Cambridge is in our Boston & Nearby zone — delivery from $19, same-day, flat $15 on orders $85+.',
			'angle_t'   => 'Campus & Kendall corporate flowers',
			'angle_b'   => 'We deliver to Harvard and MIT for graduations, defenses and welcome gifts, and run recurring lobby and office flowers for Kendall Square labs and startups. Reception-desk deliveries, building access notes and standing weekly orders are all routine here — just tell us the building and floor.',
			'occasions' => array(
				array( 'Graduations & milestones', 'Bright, celebratory bouquets to Harvard Yard, MIT and Lesley — for the ceremony or the dinner after.' ),
				array( 'Lab & office gifting', 'Recurring desk and reception flowers for Kendall Square teams, invoiced simply.' ),
				array( 'Congratulations', 'New grant, new lab, new role — a considered arrangement that lands better than another email.' ),
			),
			'faqs'      => array(
				array( 'Do you deliver flowers to Harvard and MIT?', 'Yes — we deliver across both campuses and the surrounding squares daily, including dorms, departments and event venues. Add the building and room at checkout.' ),
				array( 'Can you set up recurring office flowers in Kendall Square?', 'Absolutely. We run standing weekly or biweekly arrangements for Cambridge offices and labs with simple invoicing and a designer’s choice of what is freshest.' ),
				array( 'What is the cut-off for same-day delivery in Cambridge?', 'Order by 1 PM ET for same-day delivery anywhere in Cambridge; later orders arrive the next day.' ),
			),
		),

		'flower-delivery-somerville' => array(
			'name'      => 'Somerville',
			'title'     => 'Same-Day Flower Delivery in Somerville, MA | Wildflower',
			'metadesc'  => 'Same-day flower delivery in Somerville — Davis Square, Union Square, Assembly Row, Ball & Teele. Order by 1 PM for hand-tied, farm-fresh bouquets.',
			'answer'    => 'Yes — Wildflower delivers hand-tied flowers same-day across Somerville, from Davis and Union Square to Assembly Row and Ball Square. Order by 1 PM ET for same-day delivery.',
			'lead'      => 'Somerville is one of our busiest neighbors — dense, creative and full of first apartments, dinner parties and just-because gestures. We weave through the squares daily, from a birthday bouquet to a Davis Square triple-decker to a housewarming at Assembly Row.',
			'zips'      => array( '02143', '02144', '02145' ),
			'areas'     => array( 'Davis Square', 'Union Square', 'Assembly Row', 'Ball Square', 'Teele Square', 'Winter Hill', 'East Somerville', 'Spring Hill', 'Magoun Square', 'Porter (Somerville side)' ),
			'fee'       => 'Somerville is in our Boston & Nearby zone — same-day delivery from $19, flat $15 on orders $85+.',
			'angle_t'   => 'Dinner parties & new apartments',
			'angle_b'   => 'Somerville runs on small celebrations. We deliver hostess bouquets for the dinner party in Spring Hill, housewarmings for the new place off Highland Ave, and low-key "thinking of you" arrangements that fit a small kitchen table and a shared entryway.',
			'occasions' => array(
				array( 'Housewarmings', 'For the first apartment near Davis or the new condo at Assembly Row — a bright, sturdy welcome.' ),
				array( 'Hostess & dinner parties', 'A hand-tied bouquet that thanks the host without upstaging the table.' ),
				array( 'Just because', 'The Tuesday-afternoon flowers that make a small Somerville kitchen feel like spring.' ),
			),
			'faqs'      => array(
				array( 'Do you deliver to Davis and Union Square same-day?', 'Yes — order by 1 PM ET and we deliver same-day across all of Somerville, including Davis, Union, Ball, Teele and Assembly Row.' ),
				array( 'Can you deliver to a Somerville apartment or multi-family?', 'Of course. Somerville is full of triple-deckers and walk-ups — add the unit number and any buzzer notes at checkout and our courier will get it to the right door.' ),
				array( 'How much is flower delivery in Somerville?', 'Somerville is in our closest zone, so delivery starts at $19, and it is a flat $15 on orders of $85 or more.' ),
			),
		),

		'flower-delivery-brookline' => array(
			'name'      => 'Brookline',
			'title'     => 'Same-Day Flower Delivery in Brookline, MA | Wildflower',
			'metadesc'  => 'Same-day flowers in Brookline — Coolidge Corner, Washington Square, Brookline Village & the Longwood medical area. Order by 1 PM. Get-well & sympathy welcome.',
			'answer'    => 'Yes — Wildflower delivers fresh flowers same-day throughout Brookline, from Coolidge Corner and Washington Square to Brookline Village and the Longwood Medical Area. Order by 1 PM ET for same-day.',
			'lead'      => 'Brookline blends leafy residential streets with the Longwood hospital district, so our Brookline days mix birthday and anniversary bouquets around Coolidge Corner with a steady stream of get-well and sympathy arrangements to Longwood’s hospitals — always bright, gentle and low-scent for patient rooms.',
			'zips'      => array( '02445', '02446', '02447', '02467' ),
			'areas'     => array( 'Coolidge Corner', 'Washington Square', 'Brookline Village', 'Longwood', 'Chestnut Hill', 'Beaconsfield', 'Brookline Hills', 'Corey Hill' ),
			'fee'       => 'Brookline is in our Boston & Nearby zone — same-day delivery from $19, flat $15 on orders $85+.',
			'angle_t'   => 'Longwood hospitals & get-well',
			'angle_b'   => 'We deliver daily to the Longwood Medical Area — Beth Israel Deaconess, Brigham and Women’s, Children’s, Dana-Farber. Patient-room arrangements are kept bright, compact and unscented, and we add floor and unit details so they reach the right bedside, not a nurses’ station.',
			'occasions' => array(
				array( 'Get-well & hospital', 'Cheerful, low-scent bouquets to Longwood’s hospitals, sized to fit a patient bedside table.' ),
				array( 'Sympathy', 'Considered, restrained arrangements delivered with care to homes and services.' ),
				array( 'Anniversaries', 'Something elegant to a Coolidge Corner brownstone for the couples who have marked years here.' ),
			),
			'faqs'      => array(
				array( 'Do you deliver flowers to Longwood hospitals?', 'Yes — we deliver to Beth Israel Deaconess, Brigham and Women’s, Boston Children’s and Dana-Farber daily. Add the patient’s floor and room, and we keep hospital arrangements bright and low-scent.' ),
				array( 'Do you deliver same-day to Coolidge Corner and Brookline Village?', 'Yes — order by 1 PM ET for same-day delivery across all of Brookline, including Coolidge Corner, Washington Square and Brookline Village.' ),
				array( 'Can you make a sympathy arrangement for a Brookline service?', 'We can. Tell us the timing and tone and we will design something appropriate and deliver it discreetly to the home, funeral home or service.' ),
			),
		),

		'flower-delivery-newton' => array(
			'name'      => 'Newton',
			'title'     => 'Same-Day Flower Delivery in Newton, MA | Wildflower',
			'metadesc'  => 'Fresh flower delivery across Newton’s villages — Newton Centre, Newtonville, West Newton, Waban, Chestnut Hill & more. Same-day by 1 PM. Weddings welcome.',
			'answer'    => 'Yes — Wildflower delivers farm-fresh flowers same-day across Newton and its villages, from Newton Centre and Newtonville to Waban, Auburndale and Chestnut Hill. Order by 1 PM ET for same-day.',
			'lead'      => 'Newton — the Garden City — is thirteen leafy villages of family homes, gardens and gatherings. Our Newton deliveries lean toward anniversaries, birthdays and the celebrations that fill a house: garden-style bouquets to Newton Centre, arrangements for a Waban dinner, and full florals for the weddings and showers the village hosts through the season.',
			'zips'      => array( '02458', '02459', '02460', '02461', '02462', '02464', '02465', '02466', '02467', '02468' ),
			'areas'     => array( 'Newton Centre', 'Newtonville', 'West Newton', 'Auburndale', 'Waban', 'Chestnut Hill', 'Newton Highlands', 'Newton Corner', 'Nonantum', 'Newton Upper Falls', 'Newton Lower Falls' ),
			'fee'       => 'Newton is in our Greater Boston zone — same-day delivery from $25, flat $15 on orders $85+.',
			'angle_t'   => 'Weddings, showers & the garden aesthetic',
			'angle_b'   => 'Newton’s gardens set the tone: our arrangements here lean airy and garden-style, and we take on the bigger moments too — bridal and event florals for Newton weddings and showers, plus recurring home flowers for the households that like fresh stems every week.',
			'occasions' => array(
				array( 'Weddings & showers', 'Bridal bouquets and event florals for Newton celebrations, designed to your palette.' ),
				array( 'Anniversaries & birthdays', 'Generous, garden-style arrangements to Newton Centre, Waban and Chestnut Hill.' ),
				array( 'Weekly home flowers', 'A standing order of seasonal stems for the households that keep fresh flowers on the table.' ),
			),
			'faqs'      => array(
				array( 'Which Newton villages do you deliver to?', 'All of them — Newton Centre, Newtonville, West Newton, Auburndale, Waban, Chestnut Hill, Newton Highlands, Newton Corner, Nonantum, Upper and Lower Falls.' ),
				array( 'Do you do wedding flowers in Newton?', 'Yes — from bridal bouquets to full ceremony and reception florals. Start a custom request and we will set up a short consultation.' ),
				array( 'Is same-day delivery available in Newton?', 'Yes — order by 1 PM ET for same-day delivery across Newton. Newton is in our Greater Boston zone, with delivery from $25.' ),
			),
		),

	);

	/**
	 * Filter the delivery city landing pages.
	 *
	 * @param array $cities Keyed by page slug.
	 */
	return apply_filters( 'wildflower_delivery_cities', $cities );
}

/**
 * The city data for the current page (by slug), or null.
 *
 * @return array<string, mixed>|null
 */
function wildflower_current_city() {
	$post = get_post();
	if ( ! $post ) {
		return null;
	}
	$cities = wildflower_delivery_cities();
	return isset( $cities[ $post->post_name ] ) ? $cities[ $post->post_name ] : null;
}
