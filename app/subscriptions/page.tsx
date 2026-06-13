import type { Metadata } from 'next'
import { SubscriptionsClient } from '@/components/subscriptions/subscriptions-client'
import { JsonLd } from '@/components/json-ld'
import { breadcrumbLd } from '@/lib/seo'

export const metadata: Metadata = {
  title: 'Flower Subscriptions',
  description:
    'Fresh, seasonal flowers delivered weekly, biweekly or monthly across Greater Boston. From $55 a delivery. Pause, skip or cancel anytime.',
  alternates: { canonical: '/subscriptions' },
}

export default function SubscriptionsPage() {
  return (
    <>
      <JsonLd
        data={breadcrumbLd([
          { name: 'Home', path: '/' },
          { name: 'Subscriptions', path: '/subscriptions' },
        ])}
      />
      <SubscriptionsClient />
    </>
  )
}
