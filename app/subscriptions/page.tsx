import type { Metadata } from 'next'
import { SubscriptionsClient } from '@/components/subscriptions/subscriptions-client'
import { JsonLd } from '@/components/json-ld'
import { breadcrumbLd, abs } from '@/lib/seo'
import { BRAND } from '@/lib/brand'

export const metadata: Metadata = {
  title: 'Flower Subscriptions',
  description:
    'Fresh, seasonal flowers delivered weekly, biweekly or monthly across Greater Boston. From $55 a delivery. Pause, skip or cancel anytime.',
  alternates: { canonical: '/subscriptions' },
}

const subscriptionLd = {
  '@context': 'https://schema.org',
  '@type': 'Product',
  '@id': abs('/subscriptions#product'),
  name: `${BRAND.name} Flower Subscription`,
  description:
    'A recurring delivery of seasonal, florist’s-choice bouquets across Greater Boston. Choose weekly, bi-weekly or monthly. Pause, skip or cancel anytime.',
  image: abs('/subscriptions/teaser.png'),
  brand: { '@type': 'Brand', name: BRAND.name },
  offers: {
    '@type': 'AggregateOffer',
    priceCurrency: 'USD',
    lowPrice: 55,
    highPrice: 75,
    offerCount: 3,
    availability: 'https://schema.org/InStock',
    seller: { '@id': abs('/#business') },
  },
}

export default function SubscriptionsPage() {
  return (
    <>
      <JsonLd
        data={[
          subscriptionLd,
          breadcrumbLd([
            { name: 'Home', path: '/' },
            { name: 'Subscriptions', path: '/subscriptions' },
          ]),
        ]}
      />
      <SubscriptionsClient />
    </>
  )
}
