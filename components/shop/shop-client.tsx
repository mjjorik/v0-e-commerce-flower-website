'use client'

import { useState, useMemo, useEffect } from 'react'
import { useSearchParams } from 'next/navigation'
import { AnimatePresence, motion } from 'motion/react'
import { SlidersHorizontal, X, Check } from 'lucide-react'
import { ProductCard, ProductCardSkeleton } from '@/components/product-card'
import {
  PRODUCTS,
  COLORS,
  FLOWER_TYPES,
  OCCASIONS,
} from '@/lib/products'
import { cn } from '@/lib/utils'

type SortKey = 'featured' | 'price-asc' | 'newest'

const SORTS: { key: SortKey; label: string }[] = [
  { key: 'featured', label: 'Featured' },
  { key: 'price-asc', label: 'Price: low to high' },
  { key: 'newest', label: 'Newest' },
]

const PRICE_BANDS = [
  { label: 'Any price', min: 0, max: 999 },
  { label: 'Under $75', min: 0, max: 75 },
  { label: '$75 – $100', min: 75, max: 100 },
  { label: '$100+', min: 100, max: 999 },
]

export function ShopClient() {
  const params = useSearchParams()
  const [loading, setLoading] = useState(true)
  const [sort, setSort] = useState<SortKey>('featured')
  const [band, setBand] = useState(0)
  const [colors, setColors] = useState<string[]>([])
  const [types, setTypes] = useState<string[]>([])
  const [occasions, setOccasions] = useState<string[]>([])
  const [sameDay, setSameDay] = useState(false)
  const [sheetOpen, setSheetOpen] = useState(false)

  useEffect(() => {
    const t = setTimeout(() => setLoading(false), 650)
    return () => clearTimeout(t)
  }, [])

  // hydrate from query params
  useEffect(() => {
    const max = params.get('max')
    if (max === '75') setBand(1)
    if (params.get('sort') === 'featured') setSort('featured')
    const occ = params.get('occasion')
    if (occ) setOccasions([occ])
  }, [params])

  const toggle = (
    value: string,
    list: string[],
    setter: (v: string[]) => void,
  ) => {
    setter(
      list.includes(value)
        ? list.filter((v) => v !== value)
        : [...list, value],
    )
  }

  const filtered = useMemo(() => {
    const { min, max } = PRICE_BANDS[band]
    const result = PRODUCTS.filter((p) => {
      if (p.basePrice < min || p.basePrice > max) return false
      if (colors.length && !colors.includes(p.color)) return false
      if (types.length && !types.includes(p.flowerType)) return false
      if (occasions.length && !p.occasions.some((o) => occasions.includes(o)))
        return false
      if (sameDay && !p.sameDay) return false
      return true
    })
    if (sort === 'price-asc') result.sort((a, b) => a.basePrice - b.basePrice)
    if (sort === 'newest') result.reverse()
    if (sort === 'featured')
      result.sort((a, b) => Number(!!b.bestseller) - Number(!!a.bestseller))
    return result
  }, [band, colors, types, occasions, sameDay, sort])

  const activeCount =
    (band > 0 ? 1 : 0) +
    colors.length +
    types.length +
    occasions.length +
    (sameDay ? 1 : 0)

  const clearAll = () => {
    setBand(0)
    setColors([])
    setTypes([])
    setOccasions([])
    setSameDay(false)
  }

  const Filters = () => (
    <div className="space-y-7">
      <FilterGroup title="Price">
        <div className="space-y-1.5">
          {PRICE_BANDS.map((p, i) => (
            <RadioPill
              key={p.label}
              label={p.label}
              active={band === i}
              onClick={() => setBand(i)}
            />
          ))}
        </div>
      </FilterGroup>

      <FilterGroup title="Color">
        <div className="flex flex-wrap gap-2">
          {COLORS.map((c) => (
            <CheckPill
              key={c}
              label={c}
              active={colors.includes(c)}
              onClick={() => toggle(c, colors, setColors)}
            />
          ))}
        </div>
      </FilterGroup>

      <FilterGroup title="Occasion">
        <div className="flex flex-wrap gap-2">
          {OCCASIONS.map((o) => (
            <CheckPill
              key={o}
              label={o}
              active={occasions.includes(o)}
              onClick={() => toggle(o, occasions, setOccasions)}
            />
          ))}
        </div>
      </FilterGroup>

      <FilterGroup title="Flower type">
        <div className="flex flex-wrap gap-2">
          {FLOWER_TYPES.map((t) => (
            <CheckPill
              key={t}
              label={t}
              active={types.includes(t)}
              onClick={() => toggle(t, types, setTypes)}
            />
          ))}
        </div>
      </FilterGroup>

      <FilterGroup title="Availability">
        <CheckPill
          label="Same-day delivery"
          active={sameDay}
          onClick={() => setSameDay(!sameDay)}
        />
      </FilterGroup>

      {activeCount > 0 && (
        <button
          onClick={clearAll}
          className="text-sm text-muted-foreground underline underline-offset-4 hover:text-foreground"
        >
          Clear all filters
        </button>
      )}
    </div>
  )

  return (
    <div className="mx-auto max-w-7xl px-4 pb-20 sm:px-6 lg:px-8">
      {/* top bar */}
      <div className="mb-8 flex items-center justify-between gap-4 border-y border-border py-4">
        <button
          onClick={() => setSheetOpen(true)}
          className="inline-flex items-center gap-2 text-sm lg:hidden"
        >
          <SlidersHorizontal className="size-4" />
          Filters
          {activeCount > 0 && (
            <span className="flex size-5 items-center justify-center rounded-full bg-primary text-[11px] text-primary-foreground">
              {activeCount}
            </span>
          )}
        </button>
        <p className="hidden text-sm text-muted-foreground lg:block">
          {filtered.length} bouquet{filtered.length === 1 ? '' : 's'}
        </p>
        <div className="flex items-center gap-2">
          <label className="text-sm text-muted-foreground">Sort</label>
          <select
            value={sort}
            onChange={(e) => setSort(e.target.value as SortKey)}
            className="rounded-full border border-border bg-background px-3 py-1.5 text-sm outline-none focus:border-foreground"
          >
            {SORTS.map((s) => (
              <option key={s.key} value={s.key}>
                {s.label}
              </option>
            ))}
          </select>
        </div>
      </div>

      <div className="grid gap-10 lg:grid-cols-[240px_1fr]">
        {/* desktop sidebar */}
        <aside className="hidden lg:block">
          <div className="sticky top-28">
            <Filters />
          </div>
        </aside>

        {/* grid */}
        <div>
          {filtered.length === 0 ? (
            <div className="flex flex-col items-center justify-center gap-3 py-24 text-center">
              <p className="font-serif text-2xl">Nothing matches — yet.</p>
              <p className="text-muted-foreground">
                Try loosening a filter or two.
              </p>
              <button
                onClick={clearAll}
                className="mt-2 rounded-full bg-primary px-5 py-2.5 text-sm text-primary-foreground"
              >
                Clear filters
              </button>
            </div>
          ) : (
            <div className="grid grid-cols-2 gap-x-4 gap-y-10 lg:grid-cols-3">
              {filtered.map((p, i) => (
                <ProductCard key={p.slug} product={p} index={i} />
              ))}
            </div>
          )}
        </div>
      </div>

      {/* mobile bottom sheet */}
      <AnimatePresence>
        {sheetOpen && (
          <>
            <motion.div
              initial={{ opacity: 0 }}
              animate={{ opacity: 1 }}
              exit={{ opacity: 0 }}
              onClick={() => setSheetOpen(false)}
              className="fixed inset-0 z-50 bg-foreground/30 backdrop-blur-sm lg:hidden"
            />
            <motion.div
              initial={{ y: '100%' }}
              animate={{ y: 0 }}
              exit={{ y: '100%' }}
              transition={{ type: 'spring', stiffness: 320, damping: 36 }}
              className="fixed inset-x-0 bottom-0 z-50 max-h-[85vh] overflow-y-auto rounded-t-3xl bg-background p-6 lg:hidden"
            >
              <div className="mb-5 flex items-center justify-between">
                <h2 className="font-serif text-xl">Filters</h2>
                <button
                  onClick={() => setSheetOpen(false)}
                  aria-label="Close filters"
                >
                  <X className="size-5" />
                </button>
              </div>
              <Filters />
              <button
                onClick={() => setSheetOpen(false)}
                className="mt-7 w-full rounded-full bg-primary py-3.5 text-sm text-primary-foreground"
              >
                Show {filtered.length} bouquets
              </button>
            </motion.div>
          </>
        )}
      </AnimatePresence>
    </div>
  )
}

function FilterGroup({
  title,
  children,
}: {
  title: string
  children: React.ReactNode
}) {
  return (
    <div>
      <h3 className="mb-3 text-xs uppercase tracking-[0.2em] text-muted-foreground">
        {title}
      </h3>
      {children}
    </div>
  )
}

function RadioPill({
  label,
  active,
  onClick,
}: {
  label: string
  active: boolean
  onClick: () => void
}) {
  return (
    <button
      onClick={onClick}
      className={cn(
        'flex w-full items-center justify-between rounded-lg px-3 py-2 text-left text-sm transition-colors',
        active ? 'bg-primary text-primary-foreground' : 'hover:bg-card',
      )}
    >
      {label}
      {active && <Check className="size-4" />}
    </button>
  )
}

function CheckPill({
  label,
  active,
  onClick,
}: {
  label: string
  active: boolean
  onClick: () => void
}) {
  return (
    <button
      onClick={onClick}
      className={cn(
        'rounded-full border px-3.5 py-1.5 text-sm transition-colors',
        active
          ? 'border-primary bg-primary text-primary-foreground'
          : 'border-border hover:border-foreground',
      )}
    >
      {label}
    </button>
  )
}
