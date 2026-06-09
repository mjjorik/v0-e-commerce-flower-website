import type { Metadata } from 'next'
import { SubscriptionsClient } from '@/components/subscriptions/subscriptions-client'

export const metadata: Metadata = {
  title: 'Flower Subscriptions',
  description:
    'Fresh, seasonal flowers delivered weekly, biweekly or monthly across Greater Boston. From $55 a delivery. Pause, skip or cancel anytime.',
}

export default function SubscriptionsPage() {
  return <SubscriptionsClient />
}
