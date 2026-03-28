export default function Stats() {
  return (
    <section className="bg-dark py-10">
      <div className="max-w-6xl mx-auto px-4 sm:px-6">
        <div className="grid grid-cols-1 sm:grid-cols-3 gap-8 sm:gap-6 text-center">
          <div>
            <span className="text-3xl font-bold text-white">5 000+</span>
            <p className="text-sm text-white/60 mt-1">Звонков с HR разобрано агентом</p>
          </div>
          <div>
            <span className="text-3xl font-bold text-white">46 000+</span>
            <p className="text-sm text-white/60 mt-1">Резюме в базе для анализа</p>
          </div>
          <div>
            <span className="text-3xl font-bold text-white">91%<span className="text-accent text-lg align-super">*</span></span>
            <p className="text-sm text-white/60 mt-1">Проходят ATS после разбора</p>
          </div>
        </div>
        <p className="text-center text-[11px] text-white/30 mt-6">
          * ATS — автоматическая фильтрация резюме у крупных работодателей
        </p>
      </div>
    </section>
  )
}
