const STEPS = [
  {
    n: '01',
    title: 'Pick your bouquet',
    body: 'Browse the line-up or start a subscription. Choose a size — Petite, Classic or Grand.',
  },
  {
    n: '02',
    title: 'Tell us when & where',
    body: 'Add a delivery date, a time slot and a gift message. Same-day if you order by 1 PM.',
  },
  {
    n: '03',
    title: 'We hand-deliver it',
    body: 'Our local couriers bring it fresh to the door, anywhere across Greater Boston.',
  },
]

export function HowItWorks() {
  return (
    <section className="bg-primary py-16 text-primary-foreground sm:py-24">
      <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <h2 className="mb-12 max-w-md text-balance font-serif text-3xl tracking-tight sm:text-5xl">
          How it works
        </h2>
        <div className="grid gap-10 sm:grid-cols-3 sm:gap-8">
          {STEPS.map((step, i) => (
            <div key={step.n}>
              <p className="font-serif text-6xl text-primary-foreground/40 sm:text-7xl">
                {step.n}
              </p>
              <h3 className="mt-4 font-serif text-2xl">{step.title}</h3>
              <p className="mt-2 max-w-xs text-pretty leading-relaxed text-primary-foreground/70">
                {step.body}
              </p>
            </div>
          ))}
        </div>
      </div>
    </section>
  )
}
