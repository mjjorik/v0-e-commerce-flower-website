<?php
/**
 * Journal article content, auto-provisioned as posts (no manual import).
 *
 * Written for 2026 SEO / GEO / AEO / E-E-A-T: answer-first openings,
 * comparison tables (AI-citable), FAQs, florist first-hand expertise and
 * Boston local relevance. Provisioning (inc/provision.php) creates these as
 * published posts and removes the default "Hello World" post.
 *
 * @package Wildflower
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The journal articles, newest first (the newest becomes the featured story).
 *
 * @return array<int, array<string, string>>
 */
function wildflower_journal_articles() {
	$articles = array();

	$articles[] = array(
		'slug'     => 'best-flowers-every-season-boston',
		'title'    => 'The Best Flowers for Every Season in Boston',
		'category' => 'Seasonal',
		'date'     => '2026-07-20 09:00:00',
		'excerpt'  => "A Boston florist's month-by-month guide to the freshest seasonal flowers — spring tulips to winter amaryllis — and how to choose blooms that last.",
		'content'  => <<<'HTML'
<p><strong>The best flowers for each Boston season are the ones cut nearby that week: tulips, ranunculus and lilac in spring; peonies, garden roses and dahlias in summer; chrysanthemums and amaranth in fall; amaryllis, anemone and evergreens in winter.</strong> Buying with the New England season means fresher stems, longer vase life, and better value — here is how we choose them at the studio, month by month.</p>

<h2>Why seasonal flowers last longer</h2>
<p>Seasonal stems are harvested closer to when you receive them, so they open on your table instead of in a shipping box. In our experience delivering across Greater Boston since 2015, an in-season peony or dahlia routinely outlasts an out-of-season import by several days. Seasonal also tends to cost less, because it is not being flown across the world against the clock.</p>

<h2>A season-by-season guide</h2>
<table>
<thead><tr><th>Season</th><th>At their best</th><th>Style they suit</th></tr></thead>
<tbody>
<tr><td>Spring (Mar–May)</td><td>Tulips, ranunculus, anemone, lilac, daffodils, hyacinth</td><td>Fresh, loose, garden-picked</td></tr>
<tr><td>Summer (Jun–Aug)</td><td>Peonies, garden roses, dahlias, sweet pea, hydrangea, zinnia</td><td>Lush, romantic, abundant</td></tr>
<tr><td>Fall (Sep–Nov)</td><td>Chrysanthemums, dahlias, amaranth, celosia, seeded eucalyptus</td><td>Textured, warm, moody</td></tr>
<tr><td>Winter (Dec–Feb)</td><td>Amaryllis, anemone, ranunculus, tulips, evergreens, berries</td><td>Structural, jewel-toned, festive</td></tr>
</tbody>
</table>

<h3>Spring</h3>
<p>Spring in Boston is tulip and ranunculus season. These stems keep growing in the vase, so give them room and expect a little movement. Lilac arrives briefly in May — grab it when you see it, and recut the woody stems daily.</p>

<h3>Summer</h3>
<p>Peonies are the headline act, usually early June into July, followed by dahlias and garden roses that carry the season through August. This is the most generous time of year for flowers, and our favorite for weddings.</p>

<h3>Fall</h3>
<p>As the light changes, we lean into texture: rich chrysanthemums (far more interesting than their reputation), trailing amaranth, and seeded eucalyptus. Palettes shift to rust, burgundy and bronze.</p>

<h3>Winter</h3>
<p>Winter is about structure and a little drama. Amaryllis and anemone bring bold color when the gardens are bare, and a few evergreen and berry accents make an arrangement feel like the season without tipping into kitsch.</p>

<h2>How to choose seasonal flowers</h2>
<ul>
<li><strong>Ask what came in this week.</strong> Any good florist will tell you what is freshest right now.</li>
<li><strong>Pick a palette, not a specific stem.</strong> "Blush and ivory" gives us room to use the best of what is in season.</li>
<li><strong>Trust designer's choice.</strong> It is almost always fresher and better value than a fixed recipe out of season.</li>
</ul>

<h2>FAQ</h2>
<h3>What flowers are in season in Boston right now?</h3>
<p>In mid-summer, peonies are finishing and dahlias, garden roses and hydrangea are at their peak. Ask us what arrived this week — it changes constantly.</p>
<h3>Are seasonal flowers cheaper?</h3>
<p>Usually, yes. In-season stems do not carry the cost and risk of long-distance, off-season shipping, so you get more flower for your budget.</p>
<h3>Can I still get peonies out of season?</h3>
<p>Sometimes, as imports, but they cost more and are less reliable. We would rather suggest a stunning in-season alternative than a tired off-season peony.</p>
HTML
	);

	$articles[] = array(
		'slug'     => 'how-to-make-cut-flowers-last-longer',
		'title'    => 'How to Make Cut Flowers Last Longer',
		'category' => 'Care',
		'date'     => '2026-07-14 09:00:00',
		'excerpt'  => "A Boston florist's care guide to keeping cut flowers fresh — the five habits, where to place the vase, a vase-life chart, and how to revive wilting stems.",
		'content'  => <<<'HTML'
<p><strong>To make cut flowers last longer, recut the stems at an angle, put them in clean water with flower food, change that water every two days, and keep the vase out of direct sun, heat and ripening fruit.</strong> Done consistently, these habits routinely add four to seven days of vase life. Here is the florist's version, in the order that actually matters.</p>

<h2>The five habits that matter most</h2>
<ol>
<li><strong>Recut on an angle.</strong> Trim 1–2 cm off each stem with a sharp knife or shears so it can drink. A flat cut sits on the vase bottom and seals itself.</li>
<li><strong>Start with a clean vase.</strong> Bacteria, not age, is what usually kills a bouquet. Wash the vase with soap before every use.</li>
<li><strong>Use the flower food.</strong> It feeds the bloom and slows bacteria. No packet? A crushed aspirin or a splash of clear soda plus a drop of bleach approximates it.</li>
<li><strong>Change the water every two days.</strong> Refresh it, and recut the stems each time. This one step does the most work.</li>
<li><strong>Strip leaves below the waterline.</strong> Submerged foliage rots fast and fouls the water.</li>
</ol>

<h2>Where you put the vase matters</h2>
<p>Flowers last longest somewhere cool. Keep them away from direct sunlight, heating vents, and the top of the fridge — and away from the fruit bowl, because ripening fruit releases ethylene gas that ages blooms quickly.</p>

<h2>How long different flowers last</h2>
<table>
<thead><tr><th>Flower</th><th>Typical vase life</th><th>Tip</th></tr></thead>
<tbody>
<tr><td>Chrysanthemums</td><td>2–3 weeks</td><td>The marathon runner of the vase</td></tr>
<tr><td>Carnations</td><td>2–3 weeks</td><td>Underrated and very hardy</td></tr>
<tr><td>Roses</td><td>7–12 days</td><td>Recut daily for the longest life</td></tr>
<tr><td>Tulips</td><td>5–7 days</td><td>Keep cool; they keep growing</td></tr>
<tr><td>Peonies</td><td>5–7 days</td><td>Buy in bud for maximum vase time</td></tr>
<tr><td>Hydrangea</td><td>4–7 days</td><td>Thirsty — mist and re-hydrate if wilting</td></tr>
</tbody>
</table>

<h2>Reviving a wilting bouquet</h2>
<p>If stems flop early, recut them and stand them in deep, cool water for a couple of hours — often they perk right back up. For hydrangea, a full dunk of the flower head in cool water can bring it back, because it drinks through its petals as well as its stem.</p>

<h2>FAQ</h2>
<h3>Should I refrigerate my flowers at night?</h3>
<p>You do not have to, but a cool room helps. Florists store flowers cold because every degree cooler slows aging.</p>
<h3>Does adding sugar or aspirin really work?</h3>
<p>In a pinch, yes — sugar feeds, a little acid helps stems drink, and a trace of bleach controls bacteria. Proper flower food does all three, balanced.</p>
<h3>Why did my bouquet die in three days?</h3>
<p>Almost always dirty water or a missed recut. Fresh water, a clean vase and a new angled cut are the fix.</p>
HTML
	);

	$articles[] = array(
		'slug'     => 'same-day-flower-delivery-boston-how-it-works',
		'title'    => 'Same-Day Flower Delivery in Boston: How It Works',
		'category' => 'Boston',
		'date'     => '2026-07-07 09:00:00',
		'excerpt'  => 'How same-day flower delivery in Boston works: the 1 PM cutoff, ZIP-based fees, delivering to hospitals and offices, and what same-day really means.',
		'content'  => <<<'HTML'
<p><strong>Same-day flower delivery in Boston works on a cutoff: order by 1 PM ET and a local florist hand-ties your bouquet that morning and delivers it the same afternoon.</strong> Delivery fees are set by the destination ZIP code, and orders placed after the cutoff arrive the next day. Here is exactly how it works at our studio.</p>

<h2>The 1 PM cutoff</h2>
<p>Same-day delivery depends on time to design and drive. Order before <strong>1 PM Eastern</strong> and we can arrange and deliver the same day across Boston and most of Greater Boston. After 1 PM, we schedule you for the next available day. During peak dates (Valentine's Day, Mother's Day), order earlier — those days book up.</p>

<h2>How delivery zones and fees work</h2>
<p>We calculate delivery from the destination ZIP code, mapped by distance from the studio in Brighton. The exact fee shows at checkout, so there are no surprises.</p>
<table>
<thead><tr><th>Zone</th><th>Same-day</th><th>Delivery from</th></tr></thead>
<tbody>
<tr><td>Boston &amp; Nearby</td><td>Order by 1 PM</td><td>$19</td></tr>
<tr><td>Greater Boston</td><td>Order by 1 PM</td><td>$25</td></tr>
<tr><td>Regional</td><td>By arrangement</td><td>By quote</td></tr>
</tbody>
</table>
<p>Spend $85 or more and delivery is a flat $15.</p>

<h2>Delivering to hospitals, offices and apartments</h2>
<p>We deliver to hospitals, offices, hotels and multi-family buildings every day. The key is detail: add the recipient's floor, unit, or front-desk name at checkout. For hospitals, we keep arrangements bright and low-scent and include the patient's room. For apartments and triple-deckers, a buzzer note helps our courier reach the right door.</p>

<h2>What "same-day" realistically means</h2>
<p>Same-day is same afternoon, not a specific hour. Central deliveries (Back Bay, South End, Downtown) often arrive within a few hours; farther zones land later in the day. If timing is critical, tell us and we will do our best to prioritize.</p>

<h2>FAQ</h2>
<h3>What time do I need to order for same-day delivery in Boston?</h3>
<p>Before 1 PM ET. Later orders are delivered the next day.</p>
<h3>How much is same-day flower delivery?</h3>
<p>From $19 in Boston &amp; Nearby and from $25 in Greater Boston, set by ZIP and shown at checkout. Orders of $85+ ship for a flat $15.</p>
<h3>Can you deliver to a Boston hospital the same day?</h3>
<p>Yes — order by 1 PM and add the patient's floor and room. We keep hospital arrangements compact and unscented.</p>
HTML
	);

	$articles[] = array(
		'slug'     => 'wedding-flowers-boston-seasonal-guide',
		'title'    => 'Wedding Flowers in Boston: A Seasonal Guide',
		'category' => 'Occasions',
		'date'     => '2026-06-30 09:00:00',
		'excerpt'  => 'Plan Boston wedding flowers by season: what blooms when, a planning timeline, real budget ranges, and the questions to ask your florist.',
		'content'  => <<<'HTML'
<p><strong>The best wedding flowers in Boston are the ones in season on your date: tulips and lilac in spring, peonies and garden roses in early summer, dahlias in late summer and fall, and amaryllis and anemone in winter.</strong> Choosing in-season keeps costs down and quality up. Here is how to plan Boston wedding florals by season, with timelines and budgets.</p>

<h2>What blooms when for Boston weddings</h2>
<table>
<thead><tr><th>Season</th><th>Signature wedding flowers</th><th>Mood</th></tr></thead>
<tbody>
<tr><td>Spring</td><td>Tulips, ranunculus, lilac, sweet pea, anemone</td><td>Fresh, romantic, airy</td></tr>
<tr><td>Early summer</td><td>Peonies, garden roses, foxglove, hydrangea</td><td>Lush and classic</td></tr>
<tr><td>Late summer / fall</td><td>Dahlias, zinnia, amaranth, chrysanthemum</td><td>Rich, textured, colorful</td></tr>
<tr><td>Winter</td><td>Amaryllis, anemone, ranunculus, evergreens</td><td>Dramatic, jewel-toned</td></tr>
</tbody>
</table>

<h2>A simple planning timeline</h2>
<ul>
<li><strong>8–12 months out:</strong> Book your florist, especially for peak dates. Share your date, venue, colors and a rough guest count.</li>
<li><strong>3–4 months out:</strong> Finalize the design, palette and pieces (bouquets, ceremony, centerpieces).</li>
<li><strong>2–4 weeks out:</strong> Confirm final counts and any specialty stems we need to source.</li>
</ul>

<h2>What Boston wedding flowers cost</h2>
<p>Full-service wedding florals are quoted individually, but as a guide: a bridal bouquet typically starts around $200, bridesmaid bouquets from about $85, and centerpieces from roughly $75 depending on size and season. Choosing in-season blooms and letting your florist lead on specific stems keeps you at the beautiful end of your budget.</p>

<h2>Questions to ask your florist</h2>
<ul>
<li>What is in season and stunning on my date?</li>
<li>Have you worked at my venue before?</li>
<li>What is included — delivery, setup, teardown, rentals?</li>
<li>Can we repurpose ceremony flowers at the reception?</li>
</ul>

<h2>FAQ</h2>
<h3>How far in advance should I book a wedding florist in Boston?</h3>
<p>Eight to twelve months for peak season (late spring through fall). Sooner is safer for popular Saturdays.</p>
<h3>Can I have peonies at my Boston wedding?</h3>
<p>Best in June and early July, when they are local and glorious. Outside that window we can sometimes import them, or suggest garden roses for a similar look.</p>
<h3>How do I make wedding flowers more affordable?</h3>
<p>Marry in season, prioritize a few high-impact pieces, and let your florist choose the specific stems from what is freshest that week.</p>
HTML
	);

	$articles[] = array(
		'slug'     => 'sympathy-funeral-flowers-guide',
		'title'    => 'Sympathy & Funeral Flowers: A Gentle Guide',
		'category' => 'Occasions',
		'date'     => '2026-06-23 09:00:00',
		'excerpt'  => 'A gentle guide to sympathy and funeral flowers: where to send them, which blooms are appropriate, color and scent, and what to write on the card.',
		'content'  => <<<'HTML'
<p><strong>For sympathy, send flowers to the family's home; for a funeral or wake, send to the service. White and soft-toned blooms — lilies, roses, chrysanthemums and hydrangea — are always appropriate, and a short handwritten note matters more than the size of the arrangement.</strong> Here is how to get it right with care.</p>

<h2>Where to send, and when</h2>
<ul>
<li><strong>To the home:</strong> A comforting arrangement for the family says "thinking of you" and can be sent any time in the days and weeks after a loss.</li>
<li><strong>To the service:</strong> Standing sprays and larger tributes go to the funeral home or church, timed to arrive before the service. Confirm the address and timing first.</li>
<li><strong>Check customs:</strong> Some traditions welcome flowers warmly; others prefer donations. When unsure, a home arrangement is a safe, kind choice.</li>
</ul>

<h2>What flowers to choose</h2>
<table>
<thead><tr><th>Type</th><th>Meaning</th><th>Good for</th></tr></thead>
<tbody>
<tr><td>White lilies</td><td>Peace, restored innocence</td><td>Services and home</td></tr>
<tr><td>White &amp; soft roses</td><td>Love and respect</td><td>Any sympathy gesture</td></tr>
<tr><td>Chrysanthemums</td><td>Remembrance (traditional)</td><td>Services</td></tr>
<tr><td>Hydrangea</td><td>Heartfelt, gentle</td><td>Home arrangements</td></tr>
</tbody>
</table>

<h2>Color and scent</h2>
<p>Soft, restrained palettes — white, cream, blush, pale green — feel most appropriate. If the arrangement is for a home or a small indoor service, we keep the scent gentle so it is comforting rather than overwhelming. When we know something about the person, we will happily work in a color or flower they loved.</p>

<h2>What to write on the card</h2>
<p>Keep it simple and sincere. "With deepest sympathy," "Thinking of you and your family," or "Holding you close" are always enough. You do not need the perfect words — presence is the point.</p>

<h2>FAQ</h2>
<h3>Is it better to send flowers to the home or the funeral?</h3>
<p>Both are appropriate. Home arrangements comfort the family directly; service flowers honor the person publicly. If in doubt, send to the home.</p>
<h3>What color flowers are best for sympathy?</h3>
<p>White and soft tones are traditional and safe. A loved one's favorite color is a thoughtful, welcome exception.</p>
<h3>Can you deliver a sympathy arrangement the same day in Boston?</h3>
<p>Yes, order by 1 PM. We deliver discreetly to homes, funeral homes and services across Greater Boston.</p>
HTML
	);

	$articles[] = array(
		'slug'     => 'are-flower-subscriptions-worth-it',
		'title'    => 'Are Flower Subscriptions Worth It?',
		'category' => 'Guides',
		'date'     => '2026-06-16 09:00:00',
		'excerpt'  => 'An honest look at whether flower subscriptions are worth it — who they suit, a plan comparison at $70/$95/$130, the value math, and how to get the most from one.',
		'content'  => <<<'HTML'
<p><strong>A flower subscription is worth it if you want fresh flowers at home regularly and value convenience and per-stem savings over choosing each bouquet yourself.</strong> Because subscriptions use designer's-choice seasonal stems, they usually cost less per flower than one-off orders — but they are less predictable. Here is an honest look.</p>

<h2>Who a subscription suits</h2>
<ul>
<li><strong>People who love fresh flowers at home</strong> and do not want to reorder each week.</li>
<li><strong>Gift-givers</strong> who want to send flowers on repeat without the reminders.</li>
<li><strong>Businesses</strong> — a lobby or reception that always looks cared for.</li>
</ul>
<p>If you need a specific bouquet for a specific event, a one-off custom order is the better fit.</p>

<h2>Comparing the plans</h2>
<table>
<thead><tr><th>Plan</th><th>Price / delivery</th><th>Best for</th></tr></thead>
<tbody>
<tr><td>Weekly</td><td>$70</td><td>Best value per stem; always-fresh homes</td></tr>
<tr><td>Bi-weekly</td><td>$95</td><td>Our most popular rhythm; a fuller bouquet</td></tr>
<tr><td>Monthly</td><td>$130</td><td>A generous monthly moment; low commitment</td></tr>
</tbody>
</table>
<p>All plans include free delivery, and you can pause, skip or cancel anytime.</p>

<h2>The value math</h2>
<p>Because a subscription is designer's choice from the freshest stems that week, we buy efficiently and pass that on — so you generally get more flower per dollar than a one-off. The trade is control: you receive our best of the week, not a fixed recipe. In our experience, that is exactly why regulars love it — it is always seasonal and never the same twice.</p>

<h2>Making it worth it</h2>
<ul>
<li>Pick a cadence you will actually enjoy — weekly is a lot of flowers.</li>
<li>Tell us any no-go flowers or allergies once, and we work around them every time.</li>
<li>Use skip/pause when you travel so nothing is wasted.</li>
</ul>

<h2>FAQ</h2>
<h3>Can I cancel a flower subscription anytime?</h3>
<p>Yes — pause, skip or cancel from your account, with no fees.</p>
<h3>Do I get to choose the flowers?</h3>
<p>It is designer's choice from the freshest seasonal stems, which is what keeps the value and quality high. Tell us any flowers to avoid and we will.</p>
<h3>Is a subscription cheaper than buying bouquets?</h3>
<p>Per stem, usually yes, because we buy seasonally and efficiently. You trade a little predictability for better value and freshness.</p>
HTML
	);

	$articles[] = array(
		'slug'     => 'best-get-well-flowers-boston',
		'title'    => 'The Best Get-Well Flowers to Send in Boston',
		'category' => 'Occasions',
		'date'     => '2026-06-09 09:00:00',
		'excerpt'  => 'The best get-well flowers to send in Boston: bright, low-scent, compact picks, what hospitals allow, delivery tips for Longwood, and what to write.',
		'content'  => <<<'HTML'
<p><strong>The best get-well flowers are bright, cheerful and low-scent, in a compact arrangement that fits a bedside or side table — think sunflowers, gerbera daisies, alstroemeria and roses.</strong> For hospitals, keep it small and unscented, and always include the patient's floor and room. Here is how to send get-well flowers that genuinely lift someone's day.</p>

<h2>What makes a great get-well arrangement</h2>
<ul>
<li><strong>Cheerful color.</strong> Yellows, corals and warm pinks read as hopeful and energizing.</li>
<li><strong>Low or no scent.</strong> Strong fragrance can bother someone who is unwell, especially indoors.</li>
<li><strong>Compact size.</strong> Bedside tables are small; a tidy arrangement is far more useful than a towering one.</li>
<li><strong>Long vase life.</strong> Hardy stems keep looking good through a recovery.</li>
</ul>

<h2>Best flowers to choose</h2>
<table>
<thead><tr><th>Flower</th><th>Why it works</th></tr></thead>
<tbody>
<tr><td>Sunflowers</td><td>Instantly cheerful, sturdy, long-lasting</td></tr>
<tr><td>Gerbera daisies</td><td>Bright, friendly, low scent</td></tr>
<tr><td>Alstroemeria</td><td>Very long vase life, gentle look</td></tr>
<tr><td>Roses (soft tones)</td><td>Warm and classic without being heavy</td></tr>
<tr><td>Chrysanthemums</td><td>Hardy and long-lasting for a long recovery</td></tr>
</tbody>
</table>

<h2>Sending flowers to a Boston hospital</h2>
<p>We deliver to Boston's hospitals — including the Longwood Medical Area — every day. A few tips from experience: confirm the patient is still admitted, add the floor and room number, and skip lilies and heavily scented stems, which some units limit. If a delivery cannot reach a room, staff usually hold it at the nurses' station, so a phone number for the recipient helps.</p>

<h2>What to write</h2>
<p>Keep it light and warm: "Get well soon," "Thinking of you," or a shared inside joke. Humor and normalcy are a gift when someone is stuck in a hospital bed.</p>

<h2>FAQ</h2>
<h3>Can you deliver flowers to a hospital in Boston?</h3>
<p>Yes, daily, including the Longwood hospitals. Add the patient's floor and room, and order by 1 PM for same-day.</p>
<h3>What flowers should you not send to a hospital?</h3>
<p>Avoid strongly scented flowers like lilies and stargazers, and very large arrangements. Bright, compact and low-scent is best.</p>
<h3>Are potted plants a good get-well gift?</h3>
<p>They can be, and they last — but check the patient can care for it. For a hospital stay, a tidy fresh arrangement is usually easier.</p>
HTML
	);

	return $articles;
}
