export type SizeKey = 'petite' | 'classic' | 'grand'

export type ProductSize = {
  key: SizeKey
  label: string
  price: number
  stems: string
}

export type Product = {
  slug: string
  name: string
  tagline: string
  description: string
  color: string
  flowerType: string
  occasions: string[]
  sizes: ProductSize[]
  basePrice: number
  image: string
  imageAlt: string
  hoverImage: string
  hoverImageAlt: string
  bestseller?: boolean
  seasonal?: boolean
  sameDay: boolean
  whatsInside: string[]
  careTips: string[]
}

const SIZES = (petite: number, classic: number, grand: number): ProductSize[] => [
  { key: 'petite', label: 'Petite', price: petite, stems: '12–15 stems' },
  { key: 'classic', label: 'Classic', price: classic, stems: '20–25 stems' },
  { key: 'grand', label: 'Grand', price: grand, stems: '35–40 stems' },
]

export const PRODUCTS: Product[] = [
  {
    slug: 'sunday-market',
    name: 'Sunday Market',
    tagline: 'A loose, just-picked garden gather',
    description:
      'The bouquet we make for ourselves on a slow morning — ranunculus, garden roses and a tangle of greenery, arranged the way they grow.',
    color: 'Blush',
    flowerType: 'Ranunculus',
    occasions: ['Just Because', 'Birthday'],
    sizes: SIZES(50, 85, 130),
    basePrice: 50,
    image: '/products/sunday-market.png',
    imageAlt: 'Blush ranunculus and garden rose bouquet in soft natural light',
    hoverImage: '/products/sunday-market-2.png',
    hoverImageAlt: 'Close-up of blush ranunculus petals',
    bestseller: true,
    sameDay: true,
    whatsInside: ['Ranunculus', 'Garden roses', 'Astilbe', 'Eucalyptus', 'Seasonal greenery'],
    careTips: ['Trim stems at an angle every two days', 'Refresh water daily', 'Keep out of direct sun'],
  },
  {
    slug: 'back-bay-blush',
    name: 'Back Bay Blush',
    tagline: 'Soft pinks with a little attitude',
    description:
      'Dusty pink and cream tones with airy textures — quietly romantic without trying too hard.',
    color: 'Pink',
    flowerType: 'Roses',
    occasions: ['Anniversary', 'Just Because'],
    sizes: SIZES(55, 88, 130),
    basePrice: 55,
    image: '/products/back-bay-blush.png',
    imageAlt: 'Dusty pink rose and cream bouquet on a linen surface',
    hoverImage: '/products/back-bay-blush-2.png',
    hoverImageAlt: 'Close-up of dusty pink roses',
    bestseller: true,
    sameDay: true,
    whatsInside: ['Garden roses', 'Lisianthus', 'Spray roses', 'Sweet pea', 'Silver dollar eucalyptus'],
    careTips: ['Remove leaves below the waterline', 'Change water every 2 days', 'Recut stems on arrival'],
  },
  {
    slug: 'wildfield',
    name: 'Wildfield',
    tagline: 'Meadow energy, all year round',
    description:
      'An unfussy field mix — daisies, cosmos and grasses that feel like a walk through a New England meadow.',
    color: 'White',
    flowerType: 'Daisies',
    occasions: ['Just Because', 'New Baby'],
    sizes: SIZES(50, 80, 120),
    basePrice: 50,
    image: '/products/wildfield.png',
    imageAlt: 'White daisy and meadow grass bouquet',
    hoverImage: '/products/wildfield-2.png',
    hoverImageAlt: 'Close-up of daisies and grasses',
    seasonal: true,
    sameDay: true,
    whatsInside: ['Daisies', 'Cosmos', 'Bupleurum', 'Wheat grass', 'Wax flower'],
    careTips: ['Keep cool overnight', 'Top up water daily', 'Trim every other day'],
  },
  {
    slug: 'terracotta-hour',
    name: 'Terracotta Hour',
    tagline: 'Warm tones for golden light',
    description:
      'Rust, amber and butter tones — the bouquet that looks like late-afternoon sun on a brick stoop.',
    color: 'Orange',
    flowerType: 'Dahlias',
    occasions: ['Birthday', 'Just Because'],
    sizes: SIZES(58, 92, 130),
    basePrice: 58,
    image: '/products/terracotta-hour.png',
    imageAlt: 'Rust and amber dahlia bouquet in warm light',
    hoverImage: '/products/terracotta-hour-2.png',
    hoverImageAlt: 'Close-up of amber dahlia petals',
    bestseller: true,
    seasonal: true,
    sameDay: true,
    whatsInside: ['Café au lait dahlias', 'Amber roses', 'Craspedia', 'Marigold', 'Dried grasses'],
    careTips: ['Dahlias love cold water', 'Refresh water daily', 'Display away from heat'],
  },
  {
    slug: 'sage-and-stem',
    name: 'Sage & Stem',
    tagline: 'Greens, whites and quiet',
    description:
      'A restrained, architectural arrangement of whites and foliage. Minimalist, calm, very COS.',
    color: 'White',
    flowerType: 'Tulips',
    occasions: ['Sympathy', 'Just Because'],
    sizes: SIZES(52, 85, 125),
    basePrice: 52,
    image: '/products/sage-and-stem.png',
    imageAlt: 'White tulip and green foliage minimalist bouquet',
    hoverImage: '/products/sage-and-stem-2.png',
    hoverImageAlt: 'Close-up of white tulips',
    sameDay: true,
    whatsInside: ['White tulips', 'White ranunculus', 'Olive branch', 'Ruscus', 'Italian greens'],
    careTips: ['Tulips keep growing — recut often', 'Cold water only', 'Keep upright and supported'],
  },
  {
    slug: 'somerville-sunset',
    name: 'Somerville Sunset',
    tagline: 'A burst of pink and coral',
    description:
      'Loud in the best way — corals, hot pinks and a pop of garden green. Built to make someone gasp.',
    color: 'Pink',
    flowerType: 'Peonies',
    occasions: ['Birthday', 'Anniversary'],
    sizes: SIZES(60, 95, 130),
    basePrice: 60,
    image: '/products/somerville-sunset.png',
    imageAlt: 'Coral peony and hot pink bouquet',
    hoverImage: '/products/somerville-sunset-2.png',
    hoverImageAlt: 'Close-up of coral peonies',
    bestseller: true,
    sameDay: true,
    whatsInside: ['Coral peonies', 'Hot pink ranunculus', 'Garden roses', 'Snapdragon', 'Greenery'],
    careTips: ['Let peonies open in warmth', 'Change water every 2 days', 'Recut stems on arrival'],
  },
  {
    slug: 'new-baby-cloud',
    name: 'New Baby Cloud',
    tagline: 'Soft, gentle, brand-new',
    description:
      'Whisper-soft whites and the palest blush — a calm welcome for the newest, smallest person.',
    color: 'White',
    flowerType: 'Roses',
    occasions: ['New Baby', 'Just Because'],
    sizes: SIZES(50, 82, 120),
    basePrice: 50,
    image: '/products/new-baby-cloud.png',
    imageAlt: 'Soft white and pale blush bouquet',
    hoverImage: '/products/new-baby-cloud-2.png',
    hoverImageAlt: 'Close-up of soft white roses',
    sameDay: false,
    whatsInside: ['White roses', 'Blush lisianthus', 'Baby’s breath', 'Stock', 'Dusty miller'],
    careTips: ['Keep away from drafts', 'Refresh water daily', 'Trim stems gently'],
  },
  {
    slug: 'with-sympathy',
    name: 'With Sympathy',
    tagline: 'Quiet comfort, beautifully said',
    description:
      'A composed, dignified arrangement in creams and greens for the moments words fall short.',
    color: 'White',
    flowerType: 'Lilies',
    occasions: ['Sympathy'],
    sizes: SIZES(58, 90, 130),
    basePrice: 58,
    image: '/products/with-sympathy.png',
    imageAlt: 'Cream lily and white sympathy arrangement',
    hoverImage: '/products/with-sympathy-2.png',
    hoverImageAlt: 'Close-up of cream lilies',
    sameDay: true,
    whatsInside: ['White lilies', 'Cream roses', 'White stock', 'Eucalyptus', 'Italian ruscus'],
    careTips: ['Remove lily pollen to avoid stains', 'Refresh water daily', 'Keep cool'],
  },
]

export type AddOn = {
  slug: string
  name: string
  price: number
  image: string
  imageAlt: string
}

export const ADD_ONS: AddOn[] = [
  {
    slug: 'ceramic-vase',
    name: 'Handmade Ceramic Vase',
    price: 38,
    image: '/addons/vase.png',
    imageAlt: 'Matte cream handmade ceramic vase',
  },
  {
    slug: 'soy-candle',
    name: 'Garden Soy Candle',
    price: 24,
    image: '/addons/candle.png',
    imageAlt: 'Garden-scented soy candle in a glass jar',
  },
  {
    slug: 'chocolate-truffles',
    name: 'Sea Salt Truffles',
    price: 18,
    image: '/addons/chocolate.png',
    imageAlt: 'Box of dark chocolate sea salt truffles',
  },
]

export const COLORS = ['Blush', 'Pink', 'White', 'Orange'] as const
export const FLOWER_TYPES = ['Ranunculus', 'Roses', 'Daisies', 'Dahlias', 'Tulips', 'Peonies', 'Lilies'] as const
export const OCCASIONS = ['Birthday', 'Anniversary', 'Sympathy', 'Just Because', 'New Baby'] as const

export function getProduct(slug: string) {
  return PRODUCTS.find((p) => p.slug === slug)
}

export function formatPrice(cents: number) {
  return `$${cents}`
}
