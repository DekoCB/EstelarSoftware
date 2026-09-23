import { useCallback, useEffect, useState } from 'react';
import useEmblaCarousel from 'embla-carousel-react';
import { AnimatePresence, motion } from 'motion/react';
import FlipCard from './FlipCard';
import './EventsCarousel.css';

/**
 * Adaptacion de "Skiper 54 / Carousel_006" (embla-carousel + framer-
 * motion): el slide actual se revela entero (`clip-path: inset(0
 * round 2rem)`) mientras los demas quedan recortados arriba/abajo
 * (`inset(15% 0 15% 0 round 2rem)`), y el titulo aparece debajo con
 * un fundido+blur. No hay shadcn/ui ni lucide-react en este proyecto,
 * asi que en vez de los componentes <Carousel/CarouselContent/
 * CarouselItem> se usa el hook `useEmblaCarousel` directamente (lo
 * que esos componentes envuelven), y los iconos son Font Awesome (ya
 * cargado en todo el sitio) en vez de lucide-react.
 *
 * `slides`: [{ id, title, image?, fecha?, descripcion? }] — con `image`
 * pinta la foto real (object-fit:cover, como el original); sin ella,
 * un placeholder a rayas con un icono ("Proximamente" — todavia no hay
 * mas eventos reales que el primero). El slide ACTUAL con imagen se
 * monta dentro de <FlipCard> (React Bits, motion/react — ya instalado,
 * FlipCard.jsx/.css) para poder darle vuelta y ver `fecha`/`descripcion`
 * detras; con drag desactivado a proposito -el drag de FlipCard giraria
 * la tarjeta en vez de dejar que el gesto le llegue al carrusel
 * (embla), que ya usa esa misma zona para pasar de slide-. Los vecinos
 * (no actuales) y los placeholders quedan como imagen/placeholder
 * plano, sin FlipCard: no son interactivos.
 * `variant="hero"`: usado como reemplazo del banner principal del
 * evento (mas alto, ver EventsCarousel.css) — el default es la franja
 * chica de "mas eventos".
 * `theme`: clase de tema opcional para las tarjetas placeholder (p.ej.
 * "left4dead" las tine de rojo/negro acorde al arte de Go Left!!, ver
 * EventsCarousel.css) — sin tema, el rayado azulado generico.
 *
 * Las flechas y los puntos de paginacion llevan `cursor-target` para
 * que el cursor personalizado del sitio (welcome.blade.php, TargetCursor)
 * los enganche igual que a cualquier boton chico del landing -su
 * listener de hover es delegado, asi que agarra elementos montados por
 * React sin configuracion aparte-. El marco/frame de cada slide queda
 * afuera a proposito, mismo criterio que las superficies grandes del
 * landing (tarjetas del abanico, mazo, etc).
 * `onActiveChange(index)`: opcional, se llama cada vez que cambia el
 * slide actual -GoLeftShowcase lo usa para apagar el tema/acento rojo
 * en cuanto el usuario se mueve a una tarjeta "Proximamente" (index !=
 * 0), que todavia no tiene evento real ni color propio confirmado.
 */
function EventsCarousel({ slides, variant = 'default', theme, onActiveChange }) {
  const [emblaRef, emblaApi] = useEmblaCarousel({ loop: true, slidesToScroll: 1 });
  const [current, setCurrent] = useState(0);

  const onSelect = useCallback(() => {
    if (!emblaApi) return;
    const idx = emblaApi.selectedScrollSnap();
    setCurrent(idx);
    onActiveChange?.(idx);
  }, [emblaApi, onActiveChange]);

  useEffect(() => {
    if (!emblaApi) return undefined;
    onSelect();
    emblaApi.on('select', onSelect);
    return () => emblaApi.off('select', onSelect);
  }, [emblaApi, onSelect]);

  return (
    <div
      className={`evc-root${variant === 'hero' ? ' evc-root--hero' : ''}${theme ? ` evc-root--theme-${theme}` : ''}`}
    >
      <div className="evc-viewport" ref={emblaRef}>
        <div className="evc-track">
          {slides.map((slide, index) => (
            <div key={slide.id ?? index} className="evc-slide">
              <motion.div
                initial={false}
                animate={{
                  clipPath:
                    current === index
                      ? 'inset(0% 0% 0% 0% round 24px)'
                      : 'inset(15% 0% 15% 0% round 24px)',
                }}
                transition={{ duration: 0.45, ease: [0.22, 1, 0.36, 1] }}
                className="evc-frame"
              >
                {slide.image ? (
                  current === index ? (
                    <FlipCard
                      className="evc-flip"
                      front={<img src={slide.image} alt="" className="evc-image" draggable={false} />}
                      back={
                        <div className="evc-back">
                          <p className="evc-back-eyebrow">Detalles del evento</p>
                          <h3 className="evc-back-title">{slide.tituloDetalle || slide.title}</h3>
                          {slide.fecha && (
                            <p className="evc-back-fecha"><i className="fas fa-calendar" /> {slide.fecha}</p>
                          )}
                          {slide.descripcion && <p className="evc-back-desc">{slide.descripcion}</p>}
                          {slide.bases && (
                            <a
                              href={slide.bases}
                              target="_blank"
                              rel="noopener"
                              download
                              className="evc-back-bases cursor-target"
                              onClick={e => e.stopPropagation()}
                              onPointerDown={e => e.stopPropagation()}
                              onPointerUp={e => e.stopPropagation()}
                            >
                              <i className="fas fa-file-pdf" /> Descargar Bases
                            </a>
                          )}
                          <div className="evc-back-cta">
                            <span className="evc-back-cta-text">MIRA ABAJO PARA INSCRIBIRTE</span>
                            <i className="fas fa-chevron-down evc-back-cta-arrow" aria-hidden="true" />
                          </div>
                          <p className="evc-back-hint"><i className="fas fa-rotate" /> Toca para volver</p>
                        </div>
                      }
                      draggable={false}
                      tiltMax={8}
                      glareOpacity={0.14}
                      hoverScale={1.01}
                      radius={24}
                      background="#0a0705"
                      color="#f5f5f5"
                      shadow={false}
                      ariaLabel={`Ver detalles de ${slide.title}`}
                    />
                  ) : (
                    <img src={slide.image} alt="" className="evc-image" draggable={false} />
                  )
                ) : (
                  <div className="evc-placeholder" aria-hidden="true">
                    <i className="fas fa-calendar-plus" />
                  </div>
                )}
              </motion.div>

              <AnimatePresence mode="wait">
                {current === index && (
                  <motion.p
                    key={slide.title}
                    initial={{ opacity: 0, filter: 'blur(10px)' }}
                    animate={{ opacity: 1, filter: 'blur(0px)' }}
                    exit={{ opacity: 0, filter: 'blur(10px)' }}
                    transition={{ duration: 0.4 }}
                    className="evc-caption"
                  >
                    {slide.title}
                  </motion.p>
                )}
              </AnimatePresence>
            </div>
          ))}
        </div>
      </div>

      <div className="evc-nav">
        <button type="button" aria-label="Anterior" className="evc-arrow cursor-target" onClick={() => emblaApi?.scrollPrev()}>
          <i className="fas fa-chevron-left" />
        </button>
        <div className="evc-dots">
          {slides.map((slide, index) => (
            <button
              key={slide.id ?? index}
              type="button"
              aria-label={`Ir a la tarjeta ${index + 1}`}
              className={`evc-dot cursor-target${current === index ? ' is-active' : ''}`}
              onClick={() => emblaApi?.scrollTo(index)}
            />
          ))}
        </div>
        <button type="button" aria-label="Siguiente" className="evc-arrow cursor-target" onClick={() => emblaApi?.scrollNext()}>
          <i className="fas fa-chevron-right" />
        </button>
      </div>
    </div>
  );
}

export default EventsCarousel;
