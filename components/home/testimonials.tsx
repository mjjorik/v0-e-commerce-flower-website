const REVIEWS = [
  { quote: 'The nicest flowers I’ve sent, and somehow the cheapest. My sister cried.', name: 'Maya R.', hood: 'Back Bay' },
  { quote: 'Subscription is the best $55 I spend each week. The studio has taste.', name: 'Daniel K.', hood: 'Cambridge' },
  { quote: 'Ordered at noon, delivered by 4. The bouquet looked exactly like the photo.', name: 'Priya S.', hood: 'Somerville' },
  { quote: 'Finally, flowers that don’t look like a gas-station afterthought.', name: 'Tom W.', hood: 'Brookline' },
  { quote: 'Sent these for a funeral. Quietly beautiful. Thank you for getting it right.', name: 'Eleanor M.', hood: 'Newton' },
  { quote: 'My partner now expects Tuesday flowers. I have only myself to blame.', name: 'Chris L.', hood: 'Medford' },
]

export function Testimonials() {
  const row = [...REVIEWS, ...REVIEWS]
  return (
    <section className="overflow-hidden py-16 sm:py-20">
      <div className="mx-auto mb-10 max-w-7xl px-4 sm:px-6 lg:px-8">
        <p className="text-xs uppercase tracking-[0.28em] text-muted-foreground">
          Loved across the city
        </p>
        <h2 className="mt-2 font-serif text-3xl tracking-tight sm:text-5xl">
          Notes from the neighborhood
        </h2>
      </div>

      <div className="relative">
        <div className="flex w-max gap-4 animate-marquee hover:[animation-play-state:paused]">
          {row.map((r, i) => (
            <figure
              key={i}
              className="flex w-[80vw] shrink-0 flex-col justify-between rounded-2xl bg-card p-7 sm:w-96"
            >
              <blockquote className="font-serif text-xl leading-snug text-pretty">
                “{r.quote}”
              </blockquote>
              <figcaption className="mt-6 text-sm text-muted-foreground">
                <span className="font-medium text-foreground">{r.name}</span> ·{' '}
                {r.hood}
              </figcaption>
            </figure>
          ))}
        </div>
      </div>
    </section>
  )
}
