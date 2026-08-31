import type { Metadata } from 'next'
import { PageHeader } from '@/components/page-header'
import { JsonLd } from '@/components/json-ld'
import { faqLd, breadcrumbLd } from '@/lib/seo'

export const metadata: Metadata = {
  title: 'Delivery Zones & Pricing',
  description: 'Same-day flower delivery across Greater Boston. Check your zone and pricing.',
  alternates: { canonical: '/delivery' },
}

const ZONES = [
  { name: 'Boston (Downtown, Back Bay, South End)', fee: 15, sameDay: true },
  { name: 'Cambridge & Somerville', fee: 18, sameDay: true },
  { name: 'Brookline & Newton', fee: 22, sameDay: true },
  { name: 'Medford & Arlington', fee: 25, sameDay: true },
  { name: 'Quincy & Milton', fee: 28, sameDay: false },
]

const FAQS = [
  {
    q: 'Do you offer same-day flower delivery in Boston?',
    a: 'Yes. Order before 1 PM EST and we hand-deliver the same day across most Greater Boston zones, including Boston, Cambridge and Somerville. Orders placed after 1 PM arrive the next day.',
  },
  {
    q: 'How much does flower delivery cost?',
    a: 'Delivery fees range from $15 in downtown Boston to $28 for outer zones such as Quincy and Milton. The exact fee is shown at checkout based on the delivery address.',
  },
  {
    q: 'Can I pick up my order?',
    a: 'We operate as a closed studio to ensure the highest quality and fastest dispatch, so pick-up is not available at this time. Every order is hand-delivered.',
  },
  {
    q: 'How are the flowers packaged?',
    a: 'All bouquets are hydrated with eco-friendly wet wraps and packed in our signature Wildflower tote boxes to prevent tipping during transit.',
  },
]

export default function DeliveryPage() {
  return (
    <>
      <JsonLd
        data={[
          faqLd(FAQS),
          breadcrumbLd([
            { name: 'Home', path: '/' },
            { name: 'Delivery', path: '/delivery' },
          ]),
        ]}
      />
      <PageHeader
        eyebrow="Delivery"
        title="We drive them to you"
        intro="Hand-delivered across Greater Boston by couriers who actually care about keeping your flowers upright."
      />
      <section className="mx-auto max-w-3xl px-4 pb-24 sm:px-6 lg:px-8">
        <div className="mb-12 rounded-2xl bg-primary/5 p-8 text-center">
          <h2 className="font-serif text-2xl">The 1 PM Rule</h2>
          <p className="mt-2 text-muted-foreground text-pretty">
            Order before 1 PM EST for same-day delivery to most zones. Orders placed after 1 PM will be delivered the next day.
          </p>
        </div>

        <h3 className="mb-6 font-serif text-2xl">Zones & Pricing</h3>
        <ul className="divide-y divide-border border-y border-border">
          {ZONES.map((zone) => (
            <li key={zone.name} className="flex items-center justify-between py-4">
              <div>
                <p className="font-medium text-foreground">{zone.name}</p>
                <p className="text-sm text-muted-foreground">
                  {zone.sameDay ? 'Same-day eligible' : 'Next-day only'}
                </p>
              </div>
              <p className="font-serif text-lg text-terracotta">${zone.fee}</p>
            </li>
          ))}
        </ul>

        <h3 className="mt-16 mb-6 font-serif text-2xl">Frequently asked</h3>
        <div className="space-y-6 text-foreground/80">
          {FAQS.map((faq) => (
            <div key={faq.q}>
              <h4 className="font-serif text-xl text-foreground">{faq.q}</h4>
              <p className="mt-1 text-sm leading-relaxed">{faq.a}</p>
            </div>
          ))}
        </div>
      </section>
    </>
  )
}
