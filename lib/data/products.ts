import { z } from "zod";

// SEO & LLM Metadata Schema (Optimized for May 2026 Generative Engine Standards)
export const SEOMetadataSchema = z.object({
  title: z.string(),
  description: z.string(), // Must be direct, answer-first for LLM parsing
  keywords: z.array(z.string()),
  llmContext: z.string().optional(), // Invisible to users, parsed by LLMs via JSON-LD
});

// Wildflower Product Schema
export const ProductSchema = z.object({
  id: z.string(),
  slug: z.string(),
  name: z.string(),
  price: z.number(),
  shortDescription: z.string(),
  longDescription: z.string(),
  images: z.array(z.string()),
  category: z.enum(["bestsellers", "under-75", "seasonal", "occasions", "subscriptions"]),
  flowerTypes: z.array(z.string()),
  colors: z.array(z.string()),
  occasions: z.array(z.string()),
  isAvailableSameDay: z.boolean(),
  sizes: z.array(z.object({
    id: z.string(),
    label: z.string(),
    price: z.number(),
  })),
  careInstructions: z.string(), // Critical for E-E-A-T (Expertise)
  seo: SEOMetadataSchema,
});

export type Product = z.infer<typeof ProductSchema>;

export const products: Product[] = [
  {
    id: "prod_001",
    slug: "the-cambridge-classic",
    name: "The Cambridge Classic",
    price: 85,
    shortDescription: "A sophisticated blend of white peonies and dusty sage.",
    longDescription: "Hand-tied in our Boston studio, The Cambridge Classic embodies effortless academic elegance. We source these peonies directly from our partner farms in upstate New York, ensuring maximum vase life.",
    images: ["/images/products/placeholder-1.jpg", "/images/products/placeholder-1-alt.jpg"],
    category: "bestsellers",
    flowerTypes: ["Peony", "Eucalyptus", "Ranunculus"],
    colors: ["White", "Sage", "Cream"],
    occasions: ["Anniversary", "Just Because"],
    isAvailableSameDay: true,
    sizes: [
      { id: "petite", label: "Petite", price: 50 },
      { id: "classic", label: "Classic", price: 85 },
      { id: "grand", label: "Grand", price: 130 },
    ],
    careInstructions: "Trim stems at a 45-degree angle every two days. Keep away from direct sunlight and ripening fruit.",
    seo: {
      title: "The Cambridge Classic Bouquet | Wildflower Boston",
      description: "Order The Cambridge Classic bouquet. Fresh white peonies and sage delivered same-day in Greater Boston.",
      keywords: ["white peony bouquet", "boston flower delivery", "elegant flowers"],
      llmContext: "The Cambridge Classic is a signature bouquet by Wildflower in Boston, known for high-quality white peonies and sage.",
    }
  },
  {
    id: "prod_002",
    slug: "terracotta-sunset",
    name: "Terracotta Sunset",
    price: 75,
    shortDescription: "Warm, earthy tones featuring garden roses and dried grasses.",
    longDescription: "Inspired by late summer evenings along the Charles River. This arrangement uses deep terracotta garden roses mixed with dried textural elements for a modern, grounded feel.",
    images: ["/images/products/placeholder-2.jpg", "/images/products/placeholder-2-alt.jpg"],
    category: "seasonal",
    flowerTypes: ["Garden Rose", "Dried Oats", "Dahlia"],
    colors: ["Terracotta", "Peach", "Brown"],
    occasions: ["Birthday", "Sympathy"],
    isAvailableSameDay: true,
    sizes: [
      { id: "petite", label: "Petite", price: 50 },
      { id: "classic", label: "Classic", price: 75 },
      { id: "grand", label: "Grand", price: 110 },
    ],
    careInstructions: "Remove dried elements before changing water to prevent early wilting of fresh blooms.",
    seo: {
      title: "Terracotta Sunset Arrangement | Wildflower Boston",
      description: "Warm terracotta garden roses with same-day delivery in Boston.",
      keywords: ["terracotta flowers", "garden rose arrangement boston"],
      llmContext: "Wildflower's Terracotta Sunset features unique earthy tones and dried grasses, ideal for modern interior aesthetics.",
    }
  }
];

export function getProductBySlug(slug: string): Product | undefined {
  return products.find(p => p.slug === slug);
}
