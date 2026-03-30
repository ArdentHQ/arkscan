import { geoNaturalEarth1, geoPath } from "d3-geo";
import { useCallback, useEffect, useMemo, useRef, useState } from "react";
import { useTranslation } from "react-i18next";

import { IPeer } from "@/types/generated";
import { feature } from "topojson-client";
import type { Topology } from "topojson-specification";

import PlusIcon from "@icons/plus.svg?react";
import MinusIcon from "@icons/minus.svg?react";
import PeerTooltip from "./PeerTooltip";

export interface PeerGroup {
    latitude: number;
    longitude: number;
    location: string;
    count: number;
}

interface WorldMapProps {
    peers: IPeer[];
}

const DESKTOP_ASPECT_RATIO = 576 / 1152;
const MOBILE_ASPECT_RATIO = 360 / 272;
const MOBILE_BREAKPOINT = 768;

function groupPeersByLocation(peers: IPeer[]): PeerGroup[] {
    const groups = new Map<string, { latSum: number; lonSum: number; count: number; location: string }>();

    for (const peer of peers) {
        if (peer.latitude === null || peer.longitude === null) {
            continue;
        }

        const key = [peer.city, peer.country].filter(Boolean).join(", ") || `${peer.latitude},${peer.longitude}`;
        const location = [peer.city, peer.country].filter(Boolean).join(", ");

        const existing = groups.get(key);

        if (existing) {
            existing.latSum += peer.latitude;
            existing.lonSum += peer.longitude;
            existing.count++;
        } else {
            groups.set(key, {
                latSum: peer.latitude,
                lonSum: peer.longitude,
                count: 1,
                location,
            });
        }
    }

    return Array.from(groups.values()).map((group) => ({
        latitude: group.latSum / group.count,
        longitude: group.lonSum / group.count,
        location: group.location,
        count: group.count,
    }));
}

function getCssVar(name: string): string {
    return getComputedStyle(document.documentElement).getPropertyValue(name).trim();
}

export default function WorldMap({ peers }: WorldMapProps) {
    const { t } = useTranslation();
    const mapBg = getCssVar("--map-background");
    const landFill = getCssVar("--map-land");
    const landStroke = getCssVar("--map-land-borders");
    const dotColor = getCssVar("--map-peer");

    const containerRef = useRef<HTMLDivElement>(null);
    const svgRef = useRef<SVGSVGElement>(null);
    const [dimensions, setDimensions] = useState({ width: 960, height: 480 });
    const [worldData, setWorldData] = useState<GeoJSON.FeatureCollection | null>(null);
    const [hoveredGroup, setHoveredGroup] = useState<{
        group: PeerGroup;
        x: number;
        y: number;
        flipped: boolean;
    } | null>(null);
    const [zoom, setZoom] = useState(1);
    const [pan, setPan] = useState({ x: 0, y: 0 });
    const [isPanning, setIsPanning] = useState(false);
    const panStart = useRef({ x: 0, y: 0, panX: 0, panY: 0 });

    const peerGroups = useMemo(() => groupPeersByLocation(peers), [peers]);

    const pulseDelays = useMemo(() => peerGroups.map(() => Math.random() * 10), [peerGroups]);

    const pulseDurations = useMemo(() => peerGroups.map(() => 4 + Math.random() * 4), [peerGroups]);

    useEffect(() => {
        import("world-atlas/countries-110m.json").then((topology) => {
            const topo = topology.default as unknown as Topology;
            const countries = feature(topo, topo.objects.countries) as unknown as GeoJSON.FeatureCollection;
            setWorldData(countries);
        });
    }, []);

    useEffect(() => {
        const updateDimensions = () => {
            if (containerRef.current) {
                const width = containerRef.current.clientWidth;
                const ratio = width < MOBILE_BREAKPOINT ? MOBILE_ASPECT_RATIO : DESKTOP_ASPECT_RATIO;
                setDimensions({ width, height: width * ratio });
            }
        };

        updateDimensions();

        window.addEventListener("resize", updateDimensions);

        return () => window.removeEventListener("resize", updateDimensions);
    }, []);

    const { width, height } = dimensions;

    const projection = geoNaturalEarth1()
        .scale(width / 5.5)
        .translate([width / 2, height / 2]);

    const pathGenerator = geoPath().projection(projection);

    const touchMovedRef = useRef(false);

    const showTooltipForCircle = (group: PeerGroup, circle: SVGCircleElement) => {
        const container = containerRef.current;

        if (!container) {
            return;
        }

        const circleRect = circle.getBoundingClientRect();
        const containerRect = container.getBoundingClientRect();

        const x = circleRect.left + circleRect.width / 2 - containerRect.left;
        const y = circleRect.top - containerRect.top;
        const flipped = y < 60;

        setHoveredGroup({
            group,
            x,
            y,
            flipped,
        });
    };

    const handleGroupHover = (group: PeerGroup, event: React.MouseEvent<SVGCircleElement>) => {
        showTooltipForCircle(group, event.currentTarget);
    };

    const handleGroupClick = (group: PeerGroup, event: React.MouseEvent<SVGCircleElement>) => {
        if (touchMovedRef.current) {
            return;
        }

        event.stopPropagation();

        if (hoveredGroup?.group === group) {
            setHoveredGroup(null);
        } else {
            showTooltipForCircle(group, event.currentTarget);
        }
    };

    const zoomRef = useRef(zoom);
    const panRef = useRef(pan);
    const dimensionsRef = useRef(dimensions);
    zoomRef.current = zoom;
    panRef.current = pan;
    dimensionsRef.current = dimensions;

    const applyZoom = useCallback((newZoom: number, centerX?: number, centerY?: number) => {
        const clamped = Math.min(Math.max(newZoom, 1), 8);
        const { width: w, height: h } = dimensionsRef.current;

        if (clamped === 1) {
            setPan({ x: 0, y: 0 });
            setZoom(1);
            return;
        }

        const svg = svgRef.current;

        if (!svg) {
            setZoom(clamped);
            return;
        }

        const rect = svg.getBoundingClientRect();
        const mouseX = (centerX ?? rect.left + rect.width / 2) - rect.left;
        const mouseY = (centerY ?? rect.top + rect.height / 2) - rect.top;

        const svgX = (mouseX / rect.width) * w;
        const svgY = (mouseY / rect.height) * h;

        const curZoom = zoomRef.current;
        const curPan = panRef.current;

        const pointX = (svgX - w / 2 - curPan.x) / curZoom;
        const pointY = (svgY - h / 2 - curPan.y) / curZoom;

        setPan({ x: svgX - w / 2 - pointX * clamped, y: svgY - h / 2 - pointY * clamped });
        setZoom(clamped);
    }, []);

    useEffect(() => {
        const svg = svgRef.current;

        if (!svg) {
            return;
        }

        const handleWheel = (event: WheelEvent) => {
            event.preventDefault();

            const delta = event.deltaY > 0 ? -0.3 : 0.3;

            applyZoom(zoomRef.current + delta, event.clientX, event.clientY);
        };

        svg.addEventListener("wheel", handleWheel, { passive: false });

        return () => svg.removeEventListener("wheel", handleWheel);
    }, [applyZoom]);

    const handleZoomButton = (direction: number) => {
        applyZoom(zoomRef.current + direction * 0.5);
    };

    const handleMouseDown = (event: React.MouseEvent) => {
        if (zoom <= 1) {
            return;
        }

        setIsPanning(true);
        panStart.current = { x: event.clientX, y: event.clientY, panX: pan.x, panY: pan.y };
    };

    const handleMouseMove = (event: React.MouseEvent) => {
        if (!isPanning) {
            return;
        }

        const dx = event.clientX - panStart.current.x;
        const dy = event.clientY - panStart.current.y;

        setPan({
            x: panStart.current.panX + dx,
            y: panStart.current.panY + dy,
        });
    };

    const handleMouseUp = () => {
        setIsPanning(false);
    };

    const touchRef = useRef<{
        startDistance: number;
        startZoom: number;
        startPan: { x: number; y: number };
        startCenter: { x: number; y: number };
        isSingleTouch: boolean;
    }>({
        startDistance: 0,
        startZoom: 1,
        startPan: { x: 0, y: 0 },
        startCenter: { x: 0, y: 0 },
        isSingleTouch: false,
    });

    useEffect(() => {
        const svg = svgRef.current;

        if (!svg) {
            return;
        }

        const getTouchDistance = (touches: TouchList) => {
            const dx = touches[0].clientX - touches[1].clientX;
            const dy = touches[0].clientY - touches[1].clientY;
            return Math.sqrt(dx * dx + dy * dy);
        };

        const getTouchCenter = (touches: TouchList) => ({
            x: (touches[0].clientX + touches[1].clientX) / 2,
            y: (touches[0].clientY + touches[1].clientY) / 2,
        });

        const handleTouchStart = (event: TouchEvent) => {
            event.preventDefault();
            touchMovedRef.current = false;

            if (event.touches.length === 2) {
                touchRef.current = {
                    startDistance: getTouchDistance(event.touches),
                    startZoom: zoomRef.current,
                    startPan: { ...panRef.current },
                    startCenter: getTouchCenter(event.touches),
                    isSingleTouch: false,
                };
            } else if (event.touches.length === 1) {
                touchRef.current = {
                    startDistance: 0,
                    startZoom: zoomRef.current,
                    startPan: { ...panRef.current },
                    startCenter: { x: event.touches[0].clientX, y: event.touches[0].clientY },
                    isSingleTouch: true,
                };
                setIsPanning(true);
            }
        };

        const handleTouchMove = (event: TouchEvent) => {
            event.preventDefault();
            touchMovedRef.current = true;

            if (event.touches.length === 2) {
                const currentDistance = getTouchDistance(event.touches);
                const scale = currentDistance / touchRef.current.startDistance;
                const newZoom = touchRef.current.startZoom * scale;

                const center = getTouchCenter(event.touches);
                const dx = center.x - touchRef.current.startCenter.x;
                const dy = center.y - touchRef.current.startCenter.y;

                const clamped = Math.min(Math.max(newZoom, 1), 8);

                if (clamped === 1) {
                    setPan({ x: 0, y: 0 });
                    setZoom(1);
                    return;
                }

                const rect = svg.getBoundingClientRect();
                const { width: w, height: h } = dimensionsRef.current;

                const cx = ((touchRef.current.startCenter.x - rect.left) / rect.width) * w;
                const cy = ((touchRef.current.startCenter.y - rect.top) / rect.height) * h;

                const pointX = (cx - w / 2 - touchRef.current.startPan.x) / touchRef.current.startZoom;
                const pointY = (cy - h / 2 - touchRef.current.startPan.y) / touchRef.current.startZoom;

                const scaleFactor = rect.width > 0 ? w / rect.width : 1;

                setPan({
                    x: cx - w / 2 - pointX * clamped + dx * scaleFactor,
                    y: cy - h / 2 - pointY * clamped + dy * scaleFactor,
                });
                setZoom(clamped);
            } else if (event.touches.length === 1 && touchRef.current.isSingleTouch && zoomRef.current > 1) {
                const dx = event.touches[0].clientX - touchRef.current.startCenter.x;
                const dy = event.touches[0].clientY - touchRef.current.startCenter.y;

                const rect = svg.getBoundingClientRect();
                const { width: w } = dimensionsRef.current;
                const scaleFactor = rect.width > 0 ? w / rect.width : 1;

                setPan({
                    x: touchRef.current.startPan.x + dx * scaleFactor,
                    y: touchRef.current.startPan.y + dy * scaleFactor,
                });
            }
        };

        const handleTouchEnd = (event: TouchEvent) => {
            if (event.touches.length === 0) {
                setIsPanning(false);
            }
        };

        svg.addEventListener("touchstart", handleTouchStart, { passive: false });
        svg.addEventListener("touchmove", handleTouchMove, { passive: false });
        svg.addEventListener("touchend", handleTouchEnd);

        return () => {
            svg.removeEventListener("touchstart", handleTouchStart);
            svg.removeEventListener("touchmove", handleTouchMove);
            svg.removeEventListener("touchend", handleTouchEnd);
        };
    }, []);

    const dotRadius = Math.max(2, 3 / Math.sqrt(zoom));

    return (
        <div ref={containerRef} className="relative w-full">
            <div className="absolute bottom-2 right-2 z-20 flex items-center gap-1">
                {zoom > 1 && (
                    <button
                        type="button"
                        className="button-secondary h-7 px-2 py-0 text-xs"
                        onClick={() => {
                            setZoom(1);
                            setPan({ x: 0, y: 0 });
                        }}
                    >
                        {t("pages.peers-map.reset")}
                    </button>
                )}

                <button
                    type="button"
                    className="button-secondary flex h-7 w-7 items-center justify-center p-0"
                    disabled={zoom >= 8}
                    onClick={() => handleZoomButton(1)}
                >
                    <PlusIcon className="h-3 w-3" />
                </button>

                <button
                    type="button"
                    className="button-secondary flex h-7 w-7 items-center justify-center p-0"
                    disabled={zoom <= 1}
                    onClick={() => handleZoomButton(-1)}
                >
                    <MinusIcon className="h-3 w-3" />
                </button>
            </div>

            <svg
                ref={svgRef}
                viewBox={`0 0 ${width} ${height}`}
                className="w-full touch-none rounded-lg"
                style={{ background: mapBg, cursor: zoom > 1 ? (isPanning ? "grabbing" : "grab") : "default" }}
                onMouseDown={handleMouseDown}
                onMouseMove={handleMouseMove}
                onMouseUp={handleMouseUp}
                onMouseLeave={handleMouseUp}
                onClick={() => setHoveredGroup(null)}
            >
                <g
                    transform={`translate(${width / 2 + pan.x}, ${height / 2 + pan.y}) scale(${zoom}) translate(${-width / 2}, ${-height / 2})`}
                >
                    {worldData?.features.map((feature, index) => (
                        <path
                            key={index}
                            d={pathGenerator(feature) || ""}
                            fill={landFill}
                            stroke={landStroke}
                            strokeWidth={0.5 / zoom}
                        />
                    ))}

                    {peerGroups.map((group, index) => {
                        const coords = projection([group.longitude, group.latitude]);

                        if (!coords) {
                            return null;
                        }

                        const isHovered = hoveredGroup?.group === group;
                        const duration = pulseDurations[index];
                        const delay = pulseDelays[index];

                        return (
                            <g key={index}>
                                <circle
                                    cx={coords[0]}
                                    cy={coords[1]}
                                    r={dotRadius}
                                    fill={dotColor}
                                    opacity={0}
                                    style={{ pointerEvents: "none" }}
                                >
                                    <animate
                                        attributeName="r"
                                        values={`${dotRadius};${dotRadius + 5};${dotRadius}`}
                                        dur={`${duration}s`}
                                        begin={`${delay}s`}
                                        repeatCount="indefinite"
                                    />
                                    <animate
                                        attributeName="opacity"
                                        values="0.5;0;0"
                                        dur={`${duration}s`}
                                        begin={`${delay}s`}
                                        repeatCount="indefinite"
                                    />
                                </circle>

                                <circle
                                    cx={coords[0]}
                                    cy={coords[1]}
                                    r={dotRadius}
                                    fill={dotColor}
                                    opacity={isHovered ? 1 : 0.8}
                                />

                                <circle
                                    cx={coords[0]}
                                    cy={coords[1]}
                                    r={Math.max(dotRadius, 8 / zoom)}
                                    fill="transparent"
                                    className="cursor-pointer"
                                    onMouseEnter={(e) => handleGroupHover(group, e)}
                                    onMouseLeave={() => setHoveredGroup(null)}
                                    onClick={(e) => handleGroupClick(group, e)}
                                />
                            </g>
                        );
                    })}
                </g>
            </svg>

            {hoveredGroup && (
                <PeerTooltip
                    group={hoveredGroup.group}
                    x={hoveredGroup.x}
                    y={hoveredGroup.y}
                    flipped={hoveredGroup.flipped}
                />
            )}
        </div>
    );
}
