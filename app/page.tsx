import Header from '@/components/Header'
import Hero from '@/components/Hero'
import Stats from '@/components/Stats'
import Demo from '@/components/Demo'
import HowItWorks from '@/components/HowItWorks'
import Scenarios from '@/components/Scenarios'
import Pricing from '@/components/Pricing'
import Trust from '@/components/Trust'
import FAQ from '@/components/FAQ'
import Footer from '@/components/Footer'

export default function Home() {
  return (
    <>
      <Header />
      <main>
        <Hero />
        <Stats />
        <Demo />
        <HowItWorks />
        <Scenarios />
        <Pricing />
        <Trust />
        <FAQ />
      </main>
      <Footer />
    </>
  )
}
