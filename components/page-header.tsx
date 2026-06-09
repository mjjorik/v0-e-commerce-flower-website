import { KineticText } from '@/components/kinetic-text'

export function PageHeader({
  eyebrow,
  title,
  intro,
}: {
  eyebrow?: string
  title: string
  intro?: string
}) {
  return (
    <header className="mx-auto max-w-7xl px-4 pb-8 pt-10 sm:px-6 lg:px-8 lg:pb-12 lg:pt-16">
      {eyebrow && (
        <p className="mb-3 text-xs uppercase tracking-[0.28em] text-muted-foreground">
          {eyebrow}
        </p>
      )}
      <KineticText
        as="h1"
        text={title}
        className="max-w-3xl text-balance font-serif text-4xl font-medium leading-[1.0] tracking-tight sm:text-6xl"
      />
      {intro && (
        <p className="mt-5 max-w-xl text-pretty leading-relaxed text-foreground/70">
          {intro}
        </p>
      )}
    </header>
  )
}
