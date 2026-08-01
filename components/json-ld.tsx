/**
 * Renders a JSON-LD structured-data block. Server-safe (no client JS).
 * Pass any schema.org object (or array of objects).
 */
export function JsonLd({ data }: { data: object | object[] }) {
  return (
    <script
      type="application/ld+json"
      // structured data is trusted, server-generated content
      dangerouslySetInnerHTML={{ __html: JSON.stringify(data) }}
    />
  )
}
