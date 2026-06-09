import { Hero } from '@/components/home/hero'
import { CategoryRow } from '@/components/home/category-row'
import { FeaturedGrid } from '@/components/home/featured-grid'
import { ValueProps } from '@/components/home/value-props'
import { SubscriptionTeaser } from '@/components/home/subscription-teaser'
import { OccasionsBento } from '@/components/home/occasions-bento'
import { HowItWorks } from '@/components/home/how-it-works'
import { Testimonials } from '@/components/home/testimonials'
import { CommunityStrip } from '@/components/home/community-strip'
import { BottomCta } from '@/components/home/bottom-cta'

export default function HomePage() {
  return (
    <>
      <Hero />
      <CategoryRow />
      <FeaturedGrid />
      <ValueProps />
      <SubscriptionTeaser />
      <OccasionsBento />
      <HowItWorks />
      <Testimonials />
      <CommunityStrip />
      <BottomCta />
    </>
  )
}
