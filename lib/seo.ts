import { BRAND } from '@/lib/brand'
import type { Product } from '@/lib/products'

const BASE = BRAND.url

/** Absolute URL helper. */
export function abs(path = '/') {
  return new URL(path, BASE).toString()
}

/**
 * LocalBusiness / Florist — the primary entity for local SEO + E-E-A-T.
 * Establishes who we are, where we operate, hours, price range and reach.
 */
export function localBusinessLd() {
  return {
    '@context': 'https://schema.org',
    '@type': 'Florist',
    '@id': abs('/#business'),
    name: BRAND.name,
    legalName: BRAND.legalName,
    description:
      'Farm-fresh bouquets and weekly flower subscriptions, hand-delivered same-day across Greater Boston. Honestly priced from $50 to $130.',
    url: BASE,
    telephone: BRAND.phoneHref,
    email: BRAND.email,
    priceRange: BRAND.priceRange,
    foundingDate: BRAND.founded,
    image: abs('/hero/hero-bouquet.png'),
    address: {
      '@type': 'PostalAddress',
      streetAddress: BRAND.address.street,
      addressLocality: BRAND.address.locality,
      addressRegion: BRAND.address.region,
      postalCode: BRAND.address.postalCode,
      addressCountry: BRAND.address.country,
    },
    geo: {
      '@type': 'GeoCoordinates',
      latitude: BRAND.geo.lat,
      longitude: BRAND.geo.lng,
    },
    areaServed: BRAND.areasServed.map((name) => ({ '@type': 'City', name })),
    openingHoursSpecification: {
      '@type': 'OpeningHoursSpecification',
      dayOfWeek: ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
      opens: '08:00',
      closes: '16:00',
    },
    sameAs: [BRAND.social.instagram],
  }
}

/** WebSite entity with SearchAction (sitelinks search box). */
export function webSiteLd() {
  return {
    '@context': 'https://schema.org',
    '@type': 'WebSite',
    '@id': abs('/#website'),
    name: BRAND.name,
    url: BASE,
    publisher: { '@id': abs('/#business') },
    potentialAction: {
      '@type': 'SearchAction',
      target: {
        '@type': 'EntryPoint',
        urlTemplate: abs('/shop?q={search_term_string}'),
      },
      'query-input': 'required name=search_term_string',
    },
  }
}

/** Product schema with Offer — powers rich product results. */
export function productLd(product: Product) {
  const prices = product.sizes.map((s) => s.price)
  return {
    '@context': 'https://schema.org',
    '@type': 'Product',
    '@id': abs(`/shop/${product.slug}#product`),
    name: product.name,
    description: product.description,
    image: abs(product.image),
    category: 'Flowers & Bouquets',
    brand: { '@type': 'Brand', name: BRAND.name },
    offers: {
      '@type': 'AggregateOffer',
      priceCurrency: 'USD',
      lowPrice: Math.min(...prices),
      highPrice: Math.max(...prices),
      offerCount: product.sizes.length,
      availability: 'https://schema.org/InStock',
      seller: { '@id': abs('/#business') },
    },
  }
}

/** BreadcrumbList for hierarchical navigation in search results. */
export function breadcrumbLd(items: { name: string; path: string }[]) {
  return {
    '@context': 'https://schema.org',
    '@type': 'BreadcrumbList',
    itemListElement: items.map((item, i) => ({
      '@type': 'ListItem',
      position: i + 1,
      name: item.name,
      item: abs(item.path),
    })),
  }
}

/** FAQPage schema — answer-first content that LLMs and Google both reward. */
export function faqLd(faqs: { q: string; a: string }[]) {
  return {
    '@context': 'https://schema.org',
    '@type': 'FAQPage',
    mainEntity: faqs.map((f) => ({
      '@type': 'Question',
      name: f.q,
      acceptedAnswer: { '@type': 'Answer', text: f.a },
    })),
  }
}
