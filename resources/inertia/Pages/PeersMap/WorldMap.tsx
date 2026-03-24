import { geoNaturalEarth1, geoPath } from "d3-geo";
import { useEffect, useRef, useState } from "react";

import { IPeer } from "@/types/generated";
import { feature } from "topojson-client";

import type { Topology } from "topojson-specification";

interface WorldMapProps {
    peers: IPeer[];
}

const ASPECT_RATIO = 0.5;

export default function WorldMap({ peers }: WorldMapProps) {
    const containerRef = useRef<HTMLDivElement>(null);
    const [dimensions, setDimensions] = useState({ width: 960, height: 480 });
    const [worldData, setWorldData] = useState<GeoJSON.FeatureCollection | null>(null);

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
                setDimensions({ width, height: width * ASPECT_RATIO });
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

    return (
        <div ref={containerRef} className="w-full">
            <svg viewBox={`0 0 ${width} ${height}`} className="w-full" style={{ background: "#0d1117" }}>
                {worldData?.features.map((feature, index) => (
                    <path
                        key={index}
                        d={pathGenerator(feature) || ""}
                        fill="#1c2333"
                        stroke="#2d3748"
                        strokeWidth={0.5}
                    />
                ))}

                {peers.map((peer) => {
                    if (peer.latitude === null || peer.longitude === null) {
                        return null;
                    }

                    const coords = projection([peer.longitude, peer.latitude]);

                    if (!coords) {
                        return null;
                    }

                    return (
                        <g key={peer.ip}>
                            <circle cx={coords[0]} cy={coords[1]} r={6} fill="#6366f1" opacity={0.2} />
                            <circle cx={coords[0]} cy={coords[1]} r={3} fill="#818cf8" opacity={0.8} />
                        </g>
                    );
                })}
            </svg>
        </div>
    );
}
