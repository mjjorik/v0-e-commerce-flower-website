export type Occasion = {
  slug: string
  title: string
  blurb: string
  image: string
  imageAlt: string
}

export const OCCASION_CARDS: Occasion[] = [
  {
    slug: 'birthday',
    title: 'Birthday',
    blurb: 'Make their year bloom.',
    image: '/occasions/birthday.png',
    imageAlt: 'Bright celebratory birthday bouquet',
  },
  {
    slug: 'anniversary',
    title: 'Anniversary',
    blurb: 'Romance, distilled into stems.',
    image: '/occasions/anniversary.png',
    imageAlt: 'Romantic red and blush anniversary bouquet',
  },
  {
    slug: 'sympathy',
    title: 'Sympathy',
    blurb: 'When words fall short.',
    image: '/occasions/sympathy.png',
    imageAlt: 'Calm cream and green sympathy arrangement',
  },
  {
    slug: 'just-because',
    title: 'Just Because',
    blurb: 'The best reason there is.',
    image: '/occasions/just-because.png',
    imageAlt: 'Casual just-because garden bouquet',
  },
  {
    slug: 'new-baby',
    title: 'New Baby',
    blurb: 'A soft hello to someone small.',
    image: '/occasions/new-baby.png',
    imageAlt: 'Soft pastel new baby bouquet',
  },
]
