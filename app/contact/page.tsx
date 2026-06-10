import type { Metadata } from 'next'
import { PageHeader } from '@/components/page-header'
import { BRAND } from '@/lib/brand'

export const metadata: Metadata = {
  title: 'Contact Us',
  description: 'Get in touch with the Wildflower Boston team.',
}

export default function ContactPage() {
  return (
    <>
      <PageHeader
        eyebrow="Contact"
        title="We're here to help"
        intro="Have a question about an order? Need help choosing a bouquet? Let us know."
      />
      <section className="mx-auto max-w-xl px-4 pb-24 sm:px-6 lg:px-8">
        <div className="grid gap-8 sm:grid-cols-2 mb-12 border-b border-border pb-12">
          <div>
            <h3 className="font-serif text-xl">Email</h3>
            <a href={`mailto:${BRAND.email}`} className="mt-2 block text-terracotta hover:underline">
              {BRAND.email}
            </a>
            <p className="mt-1 text-sm text-muted-foreground">We reply within 2 hours during business hours.</p>
          </div>
          <div>
            <h3 className="font-serif text-xl">Text or Call</h3>
            <a href={`tel:${BRAND.phone}`} className="mt-2 block text-terracotta hover:underline">
              {BRAND.phone}
            </a>
            <p className="mt-1 text-sm text-muted-foreground">Mon-Sat, 8am-4pm EST</p>
          </div>
        </div>

        <form className="space-y-6">
          <div className="grid gap-6 sm:grid-cols-2">
            <div className="space-y-2">
              <label htmlFor="name" className="text-sm font-medium">Name</label>
              <input id="name" type="text" className="w-full rounded-xl border border-border bg-transparent px-4 py-3 outline-none focus:border-foreground" />
            </div>
            <div className="space-y-2">
              <label htmlFor="email" className="text-sm font-medium">Email</label>
              <input id="email" type="email" className="w-full rounded-xl border border-border bg-transparent px-4 py-3 outline-none focus:border-foreground" />
            </div>
          </div>
          <div className="space-y-2">
            <label htmlFor="order" className="text-sm font-medium">Order Number (Optional)</label>
            <input id="order" type="text" className="w-full rounded-xl border border-border bg-transparent px-4 py-3 outline-none focus:border-foreground" />
          </div>
          <div className="space-y-2">
            <label htmlFor="message" className="text-sm font-medium">Message</label>
            <textarea id="message" rows={5} className="w-full resize-none rounded-xl border border-border bg-transparent px-4 py-3 outline-none focus:border-foreground"></textarea>
          </div>
          <button type="button" className="rounded-full bg-primary px-8 py-3.5 text-sm font-medium text-primary-foreground hover:opacity-90">
            Send Message
          </button>
        </form>
      </section>
    </>
  )
}
