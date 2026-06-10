import type { Metadata } from 'next'
import { PageHeader } from '@/components/page-header'

export const metadata: Metadata = {
  title: 'Delivery Zones & Pricing',
  description: 'Same-day flower delivery across Greater Boston. Check your zone and pricing.',
}

const ZONES = [
  { name: 'Boston (Downtown, Back Bay, South End)', fee: 15, sameDay: true },
  { name: 'Cambridge & Somerville', fee: 18, sameDay: true },
  { name: 'Brookline & Newton', fee: 22, sameDay: true },
  { name: 'Medford & Arlington', fee: 25, sameDay: true },
  { name: 'Quincy & Milton', fee: 28, sameDay: false },
]

export default function DeliveryPage() {
  return (
    <>
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

        <div className="mt-12 space-y-6 text-foreground/80">
          <div>
            <h4 className="font-serif text-xl text-foreground">Can I pick them up?</h4>
            <p className="mt-1 text-sm">
              We currently operate as a closed studio to ensure the highest quality and fastest dispatch. Pick-up is not available at this time.
            </p>
          </div>
          <div>
            <h4 className="font-serif text-xl text-foreground">How are they packaged?</h4>
            <p className="mt-1 text-sm">
              All bouquets are hydrated with eco-friendly wet wraps and packed in our signature Wildflower tote boxes to prevent tipping during transit.
            </p>
          </div>
        </div>
      </section>
    </>
  )
}
